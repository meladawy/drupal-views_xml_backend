<?php

class views_xml_backend_handler_filter_numeric extends views_handler_filter_numeric {
  // exposed filter options
  var $no_single = TRUE;

  function option_definition() {
    $options = parent::option_definition();

    $options['xpath_selector'] = array('default' => '');

    return $options;
  }

  function options_form(&$form, &$form_state) {
    parent::options_form($form, $form_state);
    // Get a list of the available fields and arguments for token replacement.
    $options = array();
    foreach ($this->view->display_handler->get_handlers('field') as $field => $handler) {
      if ($handler->field == 'numeric') {
        $options[$handler->options['xpath_selector']] = $handler->ui_name();
      }
    }
    $form['xpath_selector'] = array(
      '#type' => 'select',
      '#title' => t('Field'),
      '#description' => t('The field name in the table that will be used as the filter.'),
      '#default_value' => $this->options['xpath_selector'],
      '#required' => TRUE,
      '#options' => $options,
    );
  }

  /**
   * Add this filter to the query.
   *
   * Due to the nature of fapi, the value and the operator have an unintended
   * level of indirection. You will find them in $this->operator
   * and $this->value respectively.
   */
  function query() {
    $this->query->add_filter($this);
  }

  function generate() {
    $options = $this->options;
    if ($options['operator'] == 'between') {
      return $options['xpath_selector'] . '>=' . $this->value['min'] . ' and ' . $options['xpath_selector'] . '<=' . $this->value['max'];
    }
    if ($options['operator'] == 'not between') {
      return $options['xpath_selector'] . '<=' . $this->value['min'] . ' or ' . $options['xpath_selector'] . '>=' . $this->value['max'];
    }
    return $this->options['xpath_selector'] . $this->options['operator'] . $this->options['value'];
  }

  function ui_name($short = FALSE) {
    if (!empty($this->options['ui_name'])) {
      $title = check_plain($this->options['ui_name']);
      return $title;
    }
    $title = ($short && isset($this->definition['title short'])) ? $this->definition['title short'] : $this->definition['title'];
    return t('!xpath: !title', array('!xpath' => $this->options['xpath_selector'], '!title' => $title));
  }
}
