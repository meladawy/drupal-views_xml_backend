<?php

/**
 * Implements hook_views_data
 */

function xml_views_views_data() {
  $data['xml']['table']['group'] = t('XML');

  $data['xml']['table']['base'] = array(
    'title' => t('XML'),
    'help' => t('Queries an XML file.'),
    'query class' => 'xml_views',
  );

  $data['xml']['text'] = array(
    'title' => t('Text'),
    'help' => t('Text in the XML file.'),
    'field' => array(
      'handler' => 'xml_views_handler_field',
      'element type' => 'div',
    ),
    'filter' => array(
      'handler' => 'xml_views_handler_filter',
    ),
    'sort' => array(
      'handler' => 'xml_views_handler_sort',
    ),
    'argument' => array(
      'handler' => 'xml_views_handler_argument_column',
    ),
  );

  $data['xml']['numeric'] = array(
    'title' => t('Numeric'),
    'help' => t('A number in the XML file.'),
    'field' => array(
      'handler' => 'xml_views_handler_field_numeric',
      'element type' => 'div',
    ),
    'filter' => array(
      'handler' => 'xml_views_handler_filter_numeric',
    ),
    'sort' => array(
      'handler' => 'xml_views_handler_sort',
    ),
    'argument' => array(
      'handler' => 'xml_views_handler_argument_column',
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
      'xml_views' => array(
        'title' => t('XML'),
        'help' => t('Reads from an XML file.'),
        'handler' => 'xml_views_plugin_query_xml',
      ),
    ),
  );
}
