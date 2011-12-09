<?php

class xml_views_handler_field_numeric extends xml_views_handler_field {
  function render($values) {
    return check_plain(trim($values[$this->field_alias]));
  }
}
