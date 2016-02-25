<?php

/**
 * @file
 * Views hooks for views_xml_backend.
 */

/**
 * Implements hook_views_data().
 */
function views_xml_backend_views_data() {
  $data = [];
  $data['views_xml_backend']['table']['group'] = t('XML');

  $data['views_xml_backend']['table']['base'] = [
    'title' => t('XML'),
    'help' => t('Queries an XML file.'),
    'query_id' => 'views_xml_backend',
  ];

  $data['views_xml_backend']['text'] = [
    'title' => t('XML Text'),
    'help' => t('Text in the XML file.'),
    'field' => ['id' => 'views_xml_backend_standard'],
    'argument' => ['id' => 'views_xml_backend_standard'],
    'filter' => ['id' => 'views_xml_backend_standard'],
    'sort' => ['id' => 'views_xml_backend_standard'],
  ];

  $data['views_xml_backend']['numeric'] = [
    'title' => t('XML Number'),
    'help' => t('A numeric value in the XML file.'),
    'field' => ['id' => 'views_xml_backend_standard'],
    'argument' => ['id' => 'views_xml_backend_numeric'],
    'filter' => ['id' => 'views_xml_backend_numeric'],
    'sort' => ['id' => 'views_xml_backend_numeric'],
  ];

  $data['views_xml_backend']['date'] = [
    'title' => t('XML Date'),
    'help' => t('Date in the XML file.'),
    'field' => ['id' => 'views_xml_backend_date'],
    'sort' => ['id' => 'views_xml_backend_date'],
  ];

  $data['views_xml_backend']['markup'] = [
    'title' => t('HTML Markup'),
    'help' => t('HTML text in the XML file.'),
    'field' => ['id' => 'views_xml_backend_markup'],
  ];

  $data['views_xml_backend']['passthrough'] = [
    'title' => t('Pass through'),
    'help' => t('This argument handler does not filter any values. It can be used to pass in values from an argument into token replacements.'),
    'argument' => ['id' => 'views_xml_backend_passthrough'],
  ];

  return $data;
}
