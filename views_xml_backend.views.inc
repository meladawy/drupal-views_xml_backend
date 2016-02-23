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
  $data['xml']['table']['group'] = t('XML');

  $data['xml']['table']['base'] = [
    'title' => t('XML'),
    'help' => t('Queries an XML file.'),
    'query_id' => 'views_xml_backend',
  ];

  $data['xml']['xml_text'] = [
    'title' => t('XML Text'),
    'help' => t('Text in the XML file.'),
    'field' => ['id' => 'xml_text'],
    'argument' => ['id' => 'views_xml_backend_standard'],
    'filter' => ['id' => 'views_xml_backend_standard'],
    'sort' => ['id' => 'views_xml_backend_standard'],
  ];

  $data['xml']['xml_date'] = [
    'title' => t('XML Date'),
    'help' => t('Date in the XML file.'),
    'field' => ['id' => 'xml_date'],
  ];

  $data['xml']['xml_markup'] = [
    'title' => t('XML Markup Text'),
    'help' => t('Markup text in the XML file.'),
    'field' => ['id' => 'xml_markup'],
  ];

  $data['xml']['passthrough'] = [
    'title' => t('Pass through'),
    'help' => t('This argument handler does not filter any values. It can be used to pass in values from an argument into token replacements.'),
    'argument' => ['handler' => 'views_xml_backend_passthrough'],
  ];

  return $data;
}
