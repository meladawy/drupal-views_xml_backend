<?php
// $Id$

class xml_views_plugin_query_xml extends views_plugin_query {

  /**
   * Constructor; Create the basic query object and fill with default values.
   */
  function init($base_table = '', $base_field, $options) {
    parent::init($base_table, $base_field, $options);

    $this->xml_file = $this->options['xml_file'];
    $this->row_xpath = $this->options['row_xpath'];
  }

  function use_pager() {
    return FALSE;
  }

  /**
   * Generate a query and a countquery from all of the information supplied
   * to the object.
   *
   * @param $get_count
   *   Provide a countquery if this is true, otherwise provide a normal query.
   */
  function query($view, $get_count = FALSE) {
    $row_xpath = $this->options['row_xpath'];
    $filter_string = '';
    if (!empty($this->filter)) {
      $filters = array();
      foreach ($this->filter as $filter) {
        $filters[] = $filter['xpath_selector'] . $filter['operator'] . $filter['value'];
      }
      $filter_string =  '[' . implode(' and ', $filters) . ']';
    }
    return $row_xpath . ($filter_string ? $filter_string);
  }

  function add_param($param, $value = '') {
    $this->params[$param] = $value;
  }

  /**
   * Builds the necessary info to execute the query.
   */
  function build(&$view) {
    $view->build_info['query'] = $this->query($view);
    $view->build_info['count_query'] = '';
    $view->build_info['query_args'] = array();
  }

  function execute(&$view) {
    $start = views_microtime();

    $file = simplexml_load_file($this->options['xml_file']);
    $rows = $file->xpath($view->build_info['query']);
    $result = array();

    foreach ($rows as $row) {
      $item = array();
      foreach ($this->fields as $field) {
        $xpath = $row->xpath($field['field']);
        $item[$field['field']] = (string)$xpath[0];
      }
      if (!empty($this->orderby)) {
        foreach ($this->orderby as $orderby) {
          $xpath = $row->xpath($orderby['field']);
          $item[$orderby['field']] = (string)$xpath[0];
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

    $view->execute_time = views_microtime() - $start;
  }

  function add_signature(&$view) {}

  function option_definition() {
    $options = parent::option_definition();
    $options['xml_file'] = array('default' => '');
    $options['row_xpath'] = array('default' => '');

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

  function add_filter($group, $operator, $xpath_selector, $value) {
    $this->filter[] = array(
      'group' => $field,
      'operator' => $operator,
      'xpath_selector' => $xpath_selector,
      'value' => $value,
    );
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
