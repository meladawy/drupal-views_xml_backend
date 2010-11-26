<?php
// $Id$

class xml_views_handler_filter extends views_handler_filter {
  // exposed filter options
  var $no_single = TRUE;

  function option_definition() {
    $options = parent::option_definition();

    $options['case'] = array('default' => TRUE);
    $options['xpath_selector'] = array('default' => '');

    return $options;
  }

  /**
   * This kind of construct makes it relatively easy for a child class
   * to add or remove functionality by overriding this function and
   * adding/removing items from this array.
   */
  function operators() {
    return array(
      '=' => array(
        'title' => t('Is equal to'),
        'short' => t('='),
        'values' => 1,
      ),
      '!=' => array(
        'title' => t('Is not equal to'),
        'short' => t('!='),
        'values' => 1,
      ),
      'contains' => array(
        'title' => t('Contains'),
        'short' => t('contains'),
        'values' => 1,
      ),
      'not' => array(
        'title' => t('Does not contain'),
        'short' => t('!has'),
        'values' => 1,
      ),
      'empty' => array(
        'title' => t('Is empty (NULL)'),
        'method' => 'op_empty',
        'short' => t('empty'),
        'values' => 0,
      ),
      'not empty' => array(
        'title' => t('Is not empty (NOT NULL)'),
        'short' => t('not empty'),
        'values' => 0,
      ),
    );
  }

  /**
   * Build strings from the operators() for 'select' options
   */
  function operator_options($which = 'title') {
    $options = array();
    foreach ($this->operators() as $id => $info) {
      $options[$id] = $info[$which];
    }

    return $options;
  }

  function options_form(&$form, &$form_state) {
    parent::options_form($form, $form_state);
    $form['xpath_selector'] = array(
      '#type' => 'textfield',
      '#title' => 'XPath selector',
      '#description' => t('The field name in the table that will be used as the filter.'),
      '#default_value' => $this->options['field_name'],
      '#required' => TRUE,
    );
  }

  function operator_values($values = 1) {
    $options = array();
    foreach ($this->operators() as $id => $info) {
      if (isset($info['values']) && $info['values'] == $values) {
        $options[] = $id;
      }
    }

    return $options;
  }

  /**
   * Provide a simple textfield for equality
   */
  function value_form(&$form, &$form_state) {
    // We have to make some choices when creating this as an exposed
    // filter form. For example, if the operator is locked and thus
    // not rendered, we can't render dependencies; instead we only
    // render the form items we need.
    $which = 'all';
    if (!empty($form['operator'])) {
      $source = ($form['operator']['#type'] == 'radios') ? 'radio:options[operator]' : 'edit-options-operator';
    }
    if (!empty($form_state['exposed'])) {
      $identifier = $this->options['expose']['identifier'];

      if (empty($this->options['expose']['use_operator']) || empty($this->options['expose']['operator'])) {
        // exposed and locked.
        $which = in_array($this->operator, $this->operator_values(1)) ? 'value' : 'none';
      }
      else {
        $source = 'edit-' . form_clean_id($this->options['expose']['operator']);
      }
    }

    if ($which == 'all' || $which == 'value') {
      $form['value'] = array(
        '#type' => 'textfield',
        '#title' => t('Value'),
        '#size' => 30,
        '#default_value' => $this->value,
      );
      if (!empty($form_state['exposed']) && !isset($form_state['input'][$identifier])) {
        $form_state['input'][$identifier] = $this->value;
      }

      if ($which == 'all') {
        $form['value'] += array(
          '#process' => array('views_process_dependency'),
          '#dependency' => array($source => $this->operator_values(1)),
        );
      }
    }

    if (!isset($form['value'])) {
      // Ensure there is something in the 'value'.
      $form['value'] = array(
        '#type' => 'value',
        '#value' => NULL
      );
    }
  }

  /**
   * Add this filter to the query.
   *
   * Due to the nature of fapi, the value and the operator have an unintended
   * level of indirection. You will find them in $this->operator
   * and $this->value respectively.
   */
  function query() {
    $this->query->add_filter($this->options['group'], $this->options['operator'], $this->options['xpath_selector'], $this->value);
  }
}
