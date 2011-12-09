<?php

class views_xml_backend_handler_field_numeric extends views_xml_backend_handler_field {
  function render($values) {
    return check_plain(trim($values[$this->field_alias]));
  }
}
