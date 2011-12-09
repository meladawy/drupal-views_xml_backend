<?php

/**
 * Implements hook_views_data
 */

function xml_views_views_data() {
  $data['xml']['table']['group'] = t('XML');

  $data['xml']['table']['base'] = array(
    'title' => t('XML'),
    'help' => t('Queries an XML file.'),
    'query class' => 'views_xml_backend',
  );

  $data['xml']['text'] = array(
    'title' => t('Text'),
    'help' => t('Text in the XML file.'),
    'field' => array(
      'handler' => 'views_xml_backend_handler_field',
      'element type' => 'div',
    ),
    'filter' => array(
      'handler' => 'views_xml_backend_handler_filter',
    ),
    'sort' => array(
      'handler' => 'views_xml_backend_handler_sort',
    ),
    'argument' => array(
      'handler' => 'views_xml_backend_handler_argument',
    ),
  );

  $data['xml']['numeric'] = array(
    'title' => t('Numeric'),
    'help' => t('A number in the XML file.'),
    'field' => array(
      'handler' => 'views_xml_backend_handler_field_numeric',
      'element type' => 'div',
    ),
    'filter' => array(
      'handler' => 'views_xml_backend_handler_filter_numeric',
    ),
    'sort' => array(
      'handler' => 'views_xml_backend_handler_sort',
    ),
    'argument' => array(
      'handler' => 'views_xml_backend_handler_argument',
    ),
  );

  return $data;
}

/**
 * Implements hook_views_plugins().
 */
function xml_views_views_plugins() {
  return array(
    'query' => array(
      'views_xml_backend' => array(
        'title' => t('XML'),
        'help' => t('Reads from an XML file.'),
        'handler' => 'views_xml_backend_plugin_query_xml.inc',
      ),
    ),
  );
}
