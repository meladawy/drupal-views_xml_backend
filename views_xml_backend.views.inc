<?php

/**
 * @file
 * Views hooks for views_xml_backend.
 */

/**
 * Implements hook_views_data().
 */
function views_xml_backend_views_data() {
  $data = array();
  $data['xml']['table']['group'] = t('XML');

  $data['xml']['table']['base'] = array(
    'title' => t('XML'),
    'help' => t('Queries an XML file.'),
    'query_id' => 'views_xml_backend',
  );

  $data['xml']['xml_text'] = array(
    'title' => t('XML Text'),
    'help' => t('Text in the XML file.'),
    'field' => array(
      'id' => 'xml_text',
    ),
    'argument' => array(
      'id' => 'string',
    ),
    'filter' => array(
      'id' => 'string',
    ),
    'sort' => array(
      'id' => 'standard',
    ),
  );

  $data['xml']['xml_date'] = array(
    'title' => t('XML Date'),
    'help' => t('Date in the XML file.'),
    'field' => array(
      'id' => 'xml_date',
     ),
  );

  $data['xml']['xml_markup'] = array(
    'title' => t('XML Markup Text'),
    'help' => t('Markup text in the XML file.'),
    'field' => array(
      'id' => 'xml_markup',
     ),
  );

  return $data;
}
