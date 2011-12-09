<?php
/**
 * @file
 * Base sort handler for views_xml_backend.
 */

class views_xml_backend_handler_sort extends views_handler_sort {
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
   * Called to add the sort to a query.
   */
  function query() {
    $this->query->add_orderby($this->table_alias, $this->options['xpath_selector'], $this->options['order']);
  }
}
