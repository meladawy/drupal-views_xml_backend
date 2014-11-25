<?php

/**
 * @file
 * Contains views_xml_backend_handler_argument_passthrough.
 */

class views_xml_backend_handler_argument_passthrough extends views_handler_argument {

  /**
   * Overrides parent::options_form().
   *
   * Only the relevant options are displayed to the user.
   */
  public function options_form(&$form, &$form_state) {
    parent::options_form($form, $form_state);
    unset($form['exception']);
  }

  /**
   * Overrides parent::query().
   *
   * Prevent the query from being changed in any way.
   */
  public function query($group_by = FALSE) {}

}
