<?php
/**
 * @file
 * Query plugin for views_xml_backend.
 */

class views_xml_backend_plugin_query_xml extends views_plugin_query {

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
    return $row_xpath . $filter_string;
  }

  /**
   * Builds the necessary info to execute the query.
   */
  function build(&$view) {
    $view->init_pager();

    // Let the pager modify the query to add limits.
    $this->pager->query();

    $view->build_info['query'] = $this->query();
    $view->build_info['count_query'] = 'count(' . $view->build_info['query'] . ')';
    $view->build_info['query_args'] = array();
  }

  function fetch_file($uri) {
    $parsed = parse_url($uri);
    // Check for local file.
    if (empty($parsed['host'])) {
      if (!file_exists($uri)) {
        drupal_set_message(t('Views XML Backend: Local file not found.'), 'error');
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
    if ($cache = cache_get($cache_file)) {
      $last_headers = $cache->data;

      if (!empty($last_headers['etag'])) {
        $headers['If-None-Match'] = $last_headers['etag'];
      }
      if (!empty($last_headers['last-modified'])) {
        $headers['If-Modified-Since'] = $last_headers['last-modified'];
      }
    }

    $result = drupal_http_request($uri, array('headers' => $headers));
    if (isset($result->error)) {
      drupal_set_message(t($result->error), 'error');
      return;
    }
    $cache_file_uri = "$destination/$cache_file";

    if ($result->code == 304) {
      if (file_exists($cache_file_uri)) {
        return file_get_contents($cache_file_uri);
      }
      // We have the headers but no cache file. :(
      // Run it back.
      cache_clear_all($cache_file, 'cache');
      return $this->fetch_file($uri);
    }

    // As learned from Feeds caching mechanism, save to file.
    file_save_data($result->data, $cache_file_uri, FILE_EXISTS_REPLACE);
    cache_set($cache_file, $result->headers);
    return $result->data;
  }

  function execute(&$view) {
    $start = microtime(TRUE);

    // Make sure that an xml file exists.
    // This could happen if you come from the add wizard to the actual views
    // edit page.
    if (empty($this->options['xml_file'])) {
      return FALSE;
    }

    $data = new stdClass();
    $data->contents = $this->fetch_file($this->options['xml_file']);
    drupal_alter('views_xml_backend_data', $data, $view->name);

    $doc = new DOMDocument();
    $doc->loadXML($data->contents);
    // If the file fails to load, bail.
    if (!$doc) {
      return;
    }

    $xpath = new DOMXPath($doc);

    // Create a simplexml object so that we can use
    // SimpleXMLElement::getNamespaces(). Does anyone know a better way to do
    // it?
    $simple = simplexml_import_dom($doc);
    if (!$simple) {
      return;
    }
    $namespaces = $simple->getNamespaces(TRUE);

    // Register namespaces. Allow for overriding the default namespace.
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
      if ($this->pager->use_count_query() || !empty($view->get_total_rows)) {
        // $this->pager->execute_count_query($count_query);
        // Hackish execute_count_query implementation.
        $this->pager->total_items = $xpath->evaluate($view->build_info['count_query']);
        if (!empty($this->pager->options['offset'])) {
          $this->pager->total_items -= $this->pager->options['offset'];
        }

        $this->pager->update_page_info();
      }

      // Let the pager modify the query to add limits.
      $this->pager->pre_execute($query);

      if (!empty($this->limit) || !empty($this->offset)) {
        $offset = intval(!empty($this->offset) ? $this->offset : 0);
        if (!empty($this->limit)) {
          $limit  = intval($this->limit) + $offset;
          $view->build_info['query'] .= "[position() > $offset and not(position() > $limit)]";
        }
        else {
          $view->build_info['query'] .= "[position() > $offset]";
        }
      }

      // Get the rows.
      $rows = $xpath->query($view->build_info['query']);
      $result = array();
      foreach ($rows as $row) {
        $item = array();

        // Query each field per row.
        foreach ($this->fields as $field) {
          $node_list = $xpath->evaluate($field['field'], $row);
          if ($node_list) {
            // Allow multiple values in a field.
            if ($field['multiple']) {
              foreach ($node_list as $node) {
                $item[$field['field']][] = $node->nodeValue;
              }
            }
            // Single value, just pull the first.
            else {
              $item[$field['field']] = $node_list->item(0)->nodeValue;
            }
          }
        }
        $result[] = $item;

      }
      if (!empty($this->orderby)) {
        // Array reverse, because the most specific are first.
        foreach (array_reverse($this->orderby) as $orderby) {
          $orderby->sort($result);
        }
      }
      $view->result = $result;
      $view->total_rows = count($result);

      $view->execute_time = microtime(TRUE) - $start;

      $this->pager->post_execute($view->result);

    }
    catch (Exception $e) {
      $view->result = array();
      if (!empty($view->live_preview)) {
        drupal_set_message(time());
        drupal_set_message($e->getMessage(), 'error');
      }
      else {
        debug('Exception: ' . $e->getMessage());
      }
    }
  }

  function add_signature(&$view) {}

  function option_definition() {
    $options = parent::option_definition();
    $options['xml_file'] = array('default' => '');
    $options['row_xpath'] = array('default' => '');
    $options['default_namespace'] = array('default' => '');

    return $options;
  }

  function options_form(&$form, &$form_state) {
    $form['xml_file'] = array(
      '#type' => 'textfield',
      '#title' => t('XML File'),
      '#default_value' => $this->options['xml_file'],
      '#description' => t("The URL or path to the XML file."),
    );
    $form['row_xpath'] = array(
      '#type' => 'textfield',
      '#title' => t('Row Xpath'),
      '#default_value' => $this->options['row_xpath'],
      '#description' => t("An xpath function that selects rows."),
      '#required' => TRUE,
    );
    $form['default_namespace'] = array(
      '#type' => 'textfield',
      '#title' => t('Default namespace'),
      '#default_value' => $this->options['default_namespace'],
      '#description' => t("If the xml contains a default namespace, it will accessible as 'default:element'. If you want something different, declare it here."),
      '#required' => FALSE,
    );
  }

  function add_field($table, $field, $alias = '', $params = array()) {
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

  function add_orderby($orderby) {

    $this->add_field($orderby->table_alias, $orderby->options['xpath_selector'], '', $orderby->options);

    $this->orderby[] = $orderby;
  }

  function add_filter($filter) {
    $this->filter[] = $filter;
  }
}
