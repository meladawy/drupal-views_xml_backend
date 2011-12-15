<?php
/**
 * @file
 * Base field handler for views_xml_backend.
 */

class views_xml_backend_handler_field extends views_handler_field {
  function render($values) {
    return check_plain($values[$this->field_alias]);
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
    $this->table_alias = 'xml';
    $this->field_alias = $this->query->add_field($this->table_alias, $this->options['xpath_selector']);
  }



  function ui_name($short = FALSE) {
    if (!empty($this->options['ui_name'])) {
      $title = check_plain($this->options['ui_name']);
      return $title;
    }
    $title = ($short && isset($this->definition['title short'])) ? $this->definition['title short'] : $this->definition['title'];
    return t('!xpath: !title', array('!xpath' => $this->options['xpath_selector'], '!title' => $title));
  }

  /**
   * Called to determine what to tell the clicksorter.
   */
  function click_sort($order) {
    if (isset($this->field_alias)) {
      $this->query->add_orderby(NULL, $this->field_alias, $order);
    }
  }
}
