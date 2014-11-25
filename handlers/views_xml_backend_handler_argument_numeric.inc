<?php

/**
 * @file
 * Contains views_xml_backend_handler_argument_numeric.
 */

class views_xml_backend_handler_argument_numeric extends views_xml_backend_handler_argument {

  public function generate() {
    return $this->options['xpath_selector'] . ' = ' . $this->get_value();
  }

}
