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

  $data['xml']['column'] = array(
    'title' => t('Column'),
    'help' => t('An column in the XML file.'),
    'field' => array(
      'handler' => 'xml_views_handler_field',
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

  return $data;
}

/**
 * Implementation of hook_views_handlers().
 */
function xml_views_views_handlers() {
  return array(
    'info' => array(
      'path' => drupal_get_path('module', 'xml_views') . '/handlers',
    ),
    'handlers' => array(
      // Fields
      'xml_views_handler_field' => array(
        'parent' => 'views_handler_field',
      ),

      // Filters
      'xml_views_handler_filter' => array(
        'parent' => 'views_handler_filter',
      ),

      // Sort handlers
      'xml_views_handler_sort' => array(
        'parent' => 'views_handler_sort',
      ),

      // Argument handlers
      'xml_views_handler_argument' => array(
        'parent' => 'views_handler_argument',
      ),
    ),
  );
}

/**
 * Implementation of hook_views_plugins().
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
