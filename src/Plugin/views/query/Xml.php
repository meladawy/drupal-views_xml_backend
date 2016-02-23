<?php

/**
 * @file
 * Contains \Drupal\views_xml_backend\Plugin\views\query\Xml.
 */

namespace Drupal\views_xml_backend\Plugin\views\query;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\join\JoinPluginBase;
use Drupal\views\Plugin\views\query\QueryPluginBase;
use Drupal\views\ResultRow;
use Drupal\views\ViewExecutable;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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

  /**
   * The cache backend.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cacheBackend;

  /**
   * The HTTP client
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * The logger.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Constructs an Xml object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The HTTP client.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   The cache backend.
   * @param \Psr\Log\LoggerInterface
   *   The logger.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ClientInterface $http_client, CacheBackendInterface $cache_backend, LoggerInterface $logger) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->httpClient = $http_client;
    $this->cacheBackend = $cache_backend;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('http_client'),
      $container->get('cache.default'),
      $container->get('logger.factory')->get('views_xml_backend')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['xml_file'] = array('default' => '');
    $options['row_xpath'] = array('default' => '');
    $options['default_namespace'] = array('default' => 'default');
    $options['show_errors'] = array('default' => TRUE);

    return $options;
  }

  /**
   * {@inheritdoc}
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
   * Ensure a table exists in the queue; if it already exists it won't
   * do anything, but if it doesn't it will add the table queue. It will ensure
   * a path leads back to the relationship table.
   *
   * @param string $table
   *   The unaliased name of the table to ensure.
   * @param string $relationship
   *   The relationship to ensure the table links to. Each relationship will
   *   get a unique instance of the table being added. If not specified,
   *   will be the primary table.
   * @param \Drupal\views\Plugin\views\join\JoinPluginBase $join
   *   A Join object (or derived object) to join the alias in.
   *
   * @return string
   *   The alias used to refer to this specific table, or NULL if the table
   *   cannot be ensured.
   */
  public function ensureTable($table, $relationship = NULL, JoinPluginBase $join = NULL) {
    return $table;
  }

  /**
   * Add a field to the query table, possibly with an alias. This will
   * automatically call ensureTable to make sure the required table
   * exists, *unless* $table is unset.
   *
   * @param string $table
   *   The table this field is attached to. If NULL, it is assumed this will
   *   be a formula; otherwise, ensureTable is used to make sure the
   *   table exists.
   * @param string $field
   *   The name of the field to add. This may be a real field or a formula.
   * @param string $alias
   *   The alias to create. If not specified, the alias will be $table_$field
   *   unless $table is NULL. When adding formulae, it is recommended that an
   *   alias be used.
   * @param array $params
   *   An array of parameters additional to the field that will control items
   *   such as aggregation functions and DISTINCT. Some values that are
   *   recognized:
   *   - function: An aggregation function to apply, such as SUM.
   *   - aggregate: Set to TRUE to indicate that this value should be
   *     aggregated in a GROUP BY.
   *
   * @return string
   *   The name that this field can be referred to as.
   */
  public function addField($table, $field, $alias = '', $params = []) {
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
   * {@inheritdoc}
   */
  public function query($get_count = FALSE) {
    $row_xpath = $this->options['row_xpath'];

    $filter_string = '';
    if (!empty($this->filter)) {
      $filters = [];
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
   * {@inheritdoc}
   */
  public function build(ViewExecutable $view) {
    $view->initPager();

    // Let the pager modify the query to add limits.
    $view->pager->query();

    $view->build_info['query'] = $this->query();
    $view->build_info['count_query'] = '';
  }

  /**
   * {@inheritdoc}
   */
  public function execute(ViewExecutable $view) {
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
    $success = $doc->loadXML($data->contents);
    // If the file fails to load, bail.
    if (!$success) {
      return;
    }

    $xpath = new \DOMXPath($doc);
    $this->registerNamespaces($xpath);

    $view->total_rows = $xpath->query($view->build_info['query'])->length;
    $view->total_rows -= $view->pager->getOffset();

    $this->calculatePager($view);

    foreach ($xpath->query($view->build_info['query']) as $index => $row) {
      $result_row = new ResultRow();
      $result_row->index = $index;
      $view->result[] = $result_row;

      foreach ($view->field as $fieldname => $field) {
        $node_list = $xpath->query($field->options['xpath_selector'], $row);

        if ($node_list === FALSE) {
          continue;
        }

        $values = [];
        foreach ($node_list as $node) {
          $values[] = $node->nodeValue;
        }
        $result_row->$fieldname = $values;
      }
    }

    $view->pager->postExecute($view->result);
    $view->pager->updatePageInfo();

    $view->execute_time = microtime(TRUE) - $start;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

  /**
   * Returns the contents of an XML file.
   *
   * @param string $uri
   *   A URL, or local file path.
   *
   * @return string
   *   The contents of the XML file.
   *
   * @throws \RuntimeException
   *   Thrown when an error occurs.
   *
   * @todo Make the exceptions more meaningful.
   */
  protected function fetchFile($uri) {
    $parsed = parse_url($uri);

    // Check for local file.
    if (empty($parsed['host'])) {
      return $this->fetchLocalFile($uri);
    }

    return $this->fetchRemoteFile($uri);
  }

  /**
   * Returns the contents of a local file.
   *
   * @param string $uri
   *   The local file path.
   *
   * @return string
   *   The file contents.
   */
  protected function fetchLocalFile($uri) {
    if (file_exists($uri)) {
      return file_get_contents($uri);
    }

    throw new \RuntimeException($this->t('Local file not found: @uri', ['@uri' => $uri]));
  }

  /**
   * Returns the contents of a remote file.
   *
   * @param string $uri
   *   The remote file URL.
   *
   * @return string
   *   The file contents.
   */
  protected function fetchRemoteFile($uri) {
    $destination = 'public://views_xml_backend';

    if (!file_prepare_directory($destination, FILE_CREATE_DIRECTORY | FILE_MODIFY_PERMISSIONS)) {
      throw new \RuntimeException($this->t('Views XML Backend directory either cannot be created or is not writable.'));
    }

    $headers = [];
    $cache_file = 'views_xml_backend_' . hash('sha256', $uri);
    $cache_file_uri = "$destination/$cache_file";

    if ($cache = $this->cacheBackend->get($cache_file)) {
      $last_headers = $cache->data;

      if (!empty($last_headers['etag'])) {
        $headers['If-None-Match'] = $last_headers['etag'];
      }
      if (!empty($last_headers['last-modified'])) {
        $headers['If-Modified-Since'] = $last_headers['last-modified'];
      }
    }

    $response = $this->httpClient->get($uri);

    if ($response->getStatusCode() === 304) {
      if (file_exists($cache_file_uri)) {
        return file_get_contents($cache_file_uri);
      }
      // We have the headers but no cache file. Run it again.
      $this->cacheBackend->delete($cache_file);

      return $this->fetchRemoteFile($uri);
    }

    $data = trim($response->getBody());

    file_unmanaged_save_data($data, $cache_file_uri, FILE_EXISTS_REPLACE);
    $this->cacheBackend->set($cache_file, array_change_key_case($response->getHeaders()));

    return $data;
  }

  /**
   * Registers available namespaces.
   *
   * @param \DOMXPath $xpath
   *   The XPath object.
   */
  protected function registerNamespaces(\DOMXPath $xpath) {
    if (!$simple = simplexml_import_dom($xpath->document)) {
      return;
    }

    foreach ($simple->getNamespaces(TRUE) as $prefix => $namespace) {
      if ($prefix === '') {
        $prefix = $this->options['default_namespace'];
      }

      $xpath->registerNamespace($prefix, $namespace);
    }
  }

  protected function calculatePager(ViewExecutable $view) {
    if (empty($this->limit) && empty($this->offset)) {
      return;
    }

    $limit  = intval(!empty($this->limit) ? $this->limit : 999999);
    $offset = intval(!empty($this->offset) ? $this->offset : 0);
    $limit += $offset;
    $view->build_info['query'] .= "[position() > $offset and not(position() > $limit)]";
  }

}
