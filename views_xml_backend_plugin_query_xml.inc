<?php

class views_xml_backend_plugin_query_xml extends views_plugin_query {

  function use_pager() {
    return FALSE;
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
      $filter_string =  '[' . implode(' and ', $filters) . ']';
    }
    return $row_xpath . ($filter_string ? $filter_string : '');
  }

  function add_param($param, $value = '') {
    $this->params[$param] = $value;
  }

  /**
   * Builds the necessary info to execute the query.
   */
  function build(&$view) {
    $view->build_info['query'] = $this->query();
    $view->build_info['count_query'] = '';
    $view->build_info['query_args'] = array();
  }

  function execute(&$view) {
    $start = microtime();

    // Make sure that an xml file exists.
    // This could for example happen if you come from the add wizard to the
    // actual views edit page.
    if (empty($this->options['xml_file'])) {
      return FALSE;
    }

    $doc = new DOMDocument();
    $doc->load($this->options['xml_file']);
    // If the file fails to load, bail.
    if (!$doc) {
      return;
    }

    $xpath = new DOMXPath($doc);

    // Register namespaces.
    $simple = simplexml_import_dom($doc);
    if (!$simple) {
      return;
    }
    $namespaces = $simple->getNamespaces(TRUE);
    foreach ($namespaces as $prefix => $namespace) {
      if ($prefix === '') {
        if (empty($this->options['default_namespace'])) {
          $xpath->registerNamespace('default', $namespace);
        }
        else {
          $xpath->registerNamespace($this->options['default_namespace'], $namespace);
        }
      }
      $xpath->registerNamespace($prefix, $namespace);
    }

    $rows = $xpath->query($view->build_info['query']);
    $result = array();
    foreach ($rows as $row) {
      $item = array();
      foreach ($this->fields as $field) {
        $node_list = $xpath->evaluate($field['field'], $row);
        $item[$field['field']] = $node_list->item(0)->nodeValue;
      }
      if (!empty($this->orderby)) {
        foreach ($this->orderby as $orderby) {
        $node_list = $xpath->evaluate($orderby['field'], $row);
        $item[$orderby['field']] = $node_list->item(0)->nodeValue;
        }
      }
      $result[] = $item;
    }
    if (!empty($this->orderby)) {
      // Array reverse, because the most specific are first - PHP works the
      // opposite way of SQL.
      foreach (array_reverse($this->orderby) as $orderby) {
        _xml_views_sort_field($orderby['field'], $orderby['order']);
        uasort($result, '_xml_views_sort');
      }
    }
    $view->result = $result;
    $view->total_rows = count($result);

    $view->execute_time = microtime() - $start;
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

    // Create a field info array.
    $field_info = array(
      'field' => $field,
      'table' => $table,
      'alias' => $field,
    ) + $params;

    if (empty($this->fields[$field])) {
      $this->fields[$field] = $field_info;
    }

    return $field;
  }

  function add_orderby($table, $field, $order, $alias = '', $params = array()) {
    $this->orderby[] = array(
      'field' => $field,
      'order' => $order,
    );
  }

  function add_filter($filter) {
    $this->filter[] = $filter;
  }
}

function _xml_views_sort_field($field = NULL, $direction = NULL) {
  static $f;
  if ($field) {
    $f = array('field' => $field, 'direction' => $direction);
  }
  return $f;
}

function _xml_views_sort($a, $b) {
  $sort = _xml_views_sort_field();
  $field = $sort['field'];
  $a_weight = (is_array($a) && isset($a[$field])) ? $a[$field] : 0;
  $b_weight = (is_array($b) && isset($b[$field])) ? $b[$field] : 0;
  if ($a_weight == $b_weight) {
    return 0;
  }
  if (strtolower($sort['direction']) == 'asc') {
    return ($a_weight < $b_weight) ? -1 : 1;
  }
  else {
    return ($a_weight > $b_weight) ? -1 : 1;
  }
}
