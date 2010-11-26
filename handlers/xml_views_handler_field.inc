<?php
// $Id$

class xml_views_handler_field extends views_handler_field {
  function render($values) {
    return check_plain($values[$this->field_alias]);
  }

  function element_type() {
    if (isset($this->definition['element type'])) {
      return $this->definition['element type'];
    }

    return 'div';
  }

  function option_definition() {
    $options = parent::option_definition();
    $options['xpath_selector'] = array('default' => '');
    return $options;
  }

  function options_form(&$form, &$form_state) {
    parent::options_form($form, $form_state);
    $form['xpath_selector'] = array(
      '#title' => t('XPath selector'),
      '#description' => t('The xpath selector'),
      '#type' => 'textfield',
      '#default_value' => $this->options['xpath_selector'],
      '#required' => TRUE,
    );
  }

  /**
   * Called to add the field to a query.
   */
  function query() {
    // Add the field.
    $this->field_alias = $this->query->add_field($this->table_alias, $this->options['xpath_selector']);
  }

  /**
   * Provide extra data to the administration form
   */
  function admin_summary() {
    return $this->label();
  }
}
