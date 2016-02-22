<?php

/**
 * @file
 * Contains \Drupal\views_xml_backend\Plugin\views\query\Xml.
 */

namespace Drupal\views_xml_backend\Plugin\views\query;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\query\QueryPluginBase;
use Drupal\views\ResultRow;
use Drupal\views\ViewExecutable;

/**
 * Views query plugin for an XML query.
 *
 * @ingroup views_query_plugins
 *
 * @ViewsQuery(
 *   id = "views_xml_backend",
 *   title = @Translation("XML Query"),
 *   help = @Translation("Query will be generated and run using the XML backend."),
 *   display_types = {"feed"}
 * )
 */
class Xml extends QueryPluginBase {

  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['xml_file'] = array('default' => '');
    $options['row_xpath'] = array('default' => '');
    $options['default_namespace'] = array('default' => '');
    $options['show_errors'] = array('default' => TRUE);

    return $options;
  }

  /**
   * Add settings for the ui.
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $form['xml_file'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('XML File'),
      '#default_value' => $this->options['xml_file'],
      '#description' => $this->t('The URL or path to the XML file.'),
      '#maxlength' => 1024,
    );
    $form['row_xpath'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Row Xpath'),
      '#default_value' => $this->options['row_xpath'],
      '#description' => $this->t('An xpath function that selects rows.'),
      '#required' => TRUE,
    );
    $form['default_namespace'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Default namespace'),
      '#default_value' => $this->options['default_namespace'],
      '#description' => $this->t("If the xml contains a default namespace, it will be accessible as 'default:element'. If you want something different, declare it here."),
    );
    $form['show_errors'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Show XML errors'),
      '#default_value' => $this->options['show_errors'],
      '#description' => $this->t('If there were any errors during XML parsing, display them. It is recommended to leave this on during development.'),
    );
  }

  /**
   * This is used by the field handler.
   */
  public function ensureTable($table, $relationship = NULL, JoinPluginBase $join = NULL) {
    return $table;
  }

  public function addField($table, $field, $alias = '', $params = array()) {
    $alias = $field;

    // Add field info array.
    if (empty($this->fields[$field])) {
      $this->fields[$field] = array(
      'field' => $field,
      'table' => $table,
      'alias' => $alias,
      ) + $params;
    }

    return $field;
  }

  /**
   * Generate a query and a countquery from all of the information supplied to
   * the object.
   *
   * @param $get_count
   *   Provide a countquery if this is true, otherwise provide a normal query.
   */
  function query($get_count = FALSE) {
    $row_xpath = $this->options['row_xpath'];

    $filter_string = '';
    if (!empty($this->filter)) {
      $filters = array();
      foreach ($this->filter as $filter) {
        $filters[] = $filter->generate();
      }
      /**
       * @todo Add an option for the filters to be 'and' or 'or'.
       */
      $filter_string =  '[' . implode(' and ', $filters) . ']';
    }
    return $row_xpath . ($filter_string ? $filter_string : '');
  }

  /**
   * Builds the necessary info to execute the query.
   */
  function build(ViewExecutable $view) {
    $view->initPager();

    // Let the pager modify the query to add limits.
    //$this->pager->query();

    $view->build_info['query'] = $this->query();
    $view->build_info['count_query'] = '';

    // Store the view in the object to be able to use it later.
    $this->view = $view;
  }

  /**
   * Executes the query and fills the associated view object with according
   * values.
   *
   * Values to set: $view->result, $view->total_rows, $view->execute_time,
   * $view->current_page.
   */
  function execute(ViewExecutable $view) {
    $start = microtime(TRUE);

    // Make sure that an xml file exists. This could happen if you come from the
    // add wizard to the actual views edit page.
    if (empty($this->options['xml_file'])) {
      return FALSE;
    }

    $data = new \stdClass();
    $data->contents = $this->fetchFile($this->options['xml_file']);
    \Drupal::moduleHandler()->alter('views_xml_backend_data', $data, $view->name);

    $doc = new \DOMDocument();
    $doc->loadXML($data->contents);
    // If the file fails to load, bail.
    if (!$doc) {
      return;
    }

    $xpath = new \DOMXPath($doc);

      // Register namespaces.
    $simple = simplexml_import_dom($doc);
    if (!$simple) {
      return;
    }
    $namespaces = $simple->getNamespaces(TRUE);
    foreach ($namespaces as $prefix => $namespace) {
      if ($prefix === '') {
        if (empty($this->options['default_namespace'])) {
          $prefix = 'default';
        }
        else {
          $prefix = $this->options['default_namespace'];
        }
      }
      $xpath->registerNamespace($prefix, $namespace);
    }

    try {
      if (!empty($view->pager->options['items_per_page']) || !empty($this->offset)) {
        // We can't have an offset without a limit, so provide a very large limit instead.
        $limit  = intval(!empty($view->pager->options['items_per_page']) ? $view->pager->options['items_per_page'] : 999999);
        $offset = intval(!empty($view->pager->options['offset']) ? $view->pager->options['offset'] : 0);
        $limit += $offset;
        $view->build_info['query'] .= "[position() > $offset and not(position() > $limit)]";
      }

      $view->total_rows = $xpath->evaluate($view->build_info['query'])->length;
      if (!empty($view->pager->options['offset'])) {
        $view->total_rows -= $view->pager->options['offset'];
      }

      $rows = $xpath->query($view->build_info['query']);

      $result = array();
      foreach ($rows as $row) {
        $item = array();
        foreach ($view->field as $fieldname => $field) {
          $node_list = $xpath->evaluate($field->options['xpath_selector'], $row);
          if ($node_list) {
            // Allow multiple values in a field.
            if ($field->options['multiple']) {
              $values = array();
              foreach ($node_list as $node) {
                $values[] = $node->nodeValue;
              }
              $item[$fieldname] = $values;
            }
            // Single value, just pull the first.
            else {
              $item[$fieldname] = $node_list->item(0)->nodeValue;
            }
          }
        }
        $result[] = new ResultRow($item);
      }
    }
    catch (Exception $e) {
      $view->result = array();
      if (!empty($view->live_preview)) {
        drupal_set_message($e->getMessage(), 'error');
      }
      else {
        debug($e->getMessage(), 'Views XML Backend');
      }
    }

    $this->view->result = $result;
  }

  protected function fetchFile($uri) {
    $parsed = parse_url($uri);

    // Check for local file.
    if (empty($parsed['host'])) {
      if (!file_exists($uri)) {
        $message = t('Local file not found: @uri', array('@uri' => $uri));
        \Drupal::logger('views_xml_backend')->error($message);
        drupal_set_message($message, 'error');
        return;
      }
      return file_get_contents($uri);
    }

    $destination = 'public://views_xml_backend';
    if (!file_prepare_directory($destination, FILE_CREATE_DIRECTORY | FILE_MODIFY_PERMISSIONS)) {
      throw new Exception(t('Views XML Backend directory either cannot be created or is not writable.'));
    }

    $headers = array();
    $cache_file = 'views_xml_backend_' . md5($uri);

    if ($cache = \Drupal::cache()->get($cache_file)) {
      $last_headers = $cache->data;

      if (!empty($last_headers['etag'])) {
        $headers['If-None-Match'] = $last_headers['etag'];
      }
      if (!empty($last_headers['last-modified'])) {
        $headers['If-Modified-Since'] = $last_headers['last-modified'];
      }

    }

    try {
      $response = \Drupal::httpClient()->get($uri);
      $data = (string) $response->getBody();
      $cache_file_uri = "$destination/$cache_file";

      if ($response->getStatusCode() == 304) {
        if (file_exists($cache_file_uri)) {
          return file_get_contents($cache_file_uri);
        }
        // We have the headers but no cache file. :(
        // Run it back.
        \Drupal::cache()->set($cache_file, NULL);
        return $this->fetch_file($uri);
      }

      file_unmanaged_save_data($data, $cache_file_uri, FILE_EXISTS_REPLACE);
      \Drupal::cache()->set($cache_file, $result->headers);
      return $data;
    }
    catch (RequestException $e) {
      if ($this->options['show_errors']) {
        drupal_set_message($e->getMessage(), 'error');
      }
      watchdog_exception('views_xml_backend', $e->getMessage());
      return FALSE;
    }
  }

}