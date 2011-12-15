<?php

/**
 * Implements hook_views_default_views().
 */
function views_xml_backend_views_default_views() {
  $views = array();

  $view = new view;
  $view->name = 'drupal_project_releases';
  $view->description = 'Releases for a project by API compatability version';
  $view->tag = 'default';
  $view->base_table = 'xml';
  $view->human_name = 'Drupal Project Releases';
  $view->core = 7;
  $view->api_version = '3.0';
  $view->disabled = TRUE; /* Edit this to true to make a default view disabled initially */

  /* Display: Master */
  $handler = $view->new_display('default', 'Master', 'default');
  $handler->display->display_options['title'] = 'Drupal Project Releases';
  $handler->display->display_options['access']['type'] = 'none';
  $handler->display->display_options['cache']['type'] = 'none';
  $handler->display->display_options['query']['type'] = 'views_query';
  $handler->display->display_options['query']['options']['xml_file'] = 'http://updates.drupal.org/release-history/drupal/7.x';
  $handler->display->display_options['query']['options']['row_xpath'] = '//project/releases/release';
  $handler->display->display_options['exposed_form']['type'] = 'basic';
  $handler->display->display_options['pager']['type'] = 'none';
  $handler->display->display_options['pager']['options']['offset'] = '0';
  $handler->display->display_options['style_plugin'] = 'table';
  $handler->display->display_options['style_options']['columns'] = array(
    'text' => 'text',
  );
  $handler->display->display_options['style_options']['default'] = '-1';
  $handler->display->display_options['style_options']['info'] = array(
    'text' => array(
      'align' => '',
      'separator' => '',
      'empty_column' => 0,
    ),
  );
  $handler->display->display_options['style_options']['override'] = 1;
  $handler->display->display_options['style_options']['sticky'] = 0;
  $handler->display->display_options['style_options']['empty_table'] = 0;
  /* Field: download_link: Text */
  $handler->display->display_options['fields']['text_1']['id'] = 'text_1';
  $handler->display->display_options['fields']['text_1']['table'] = 'xml';
  $handler->display->display_options['fields']['text_1']['field'] = 'text';
  $handler->display->display_options['fields']['text_1']['label'] = 'Download URL';
  $handler->display->display_options['fields']['text_1']['exclude'] = TRUE;
  $handler->display->display_options['fields']['text_1']['alter']['alter_text'] = 0;
  $handler->display->display_options['fields']['text_1']['alter']['make_link'] = 0;
  $handler->display->display_options['fields']['text_1']['alter']['absolute'] = 0;
  $handler->display->display_options['fields']['text_1']['alter']['external'] = 0;
  $handler->display->display_options['fields']['text_1']['alter']['replace_spaces'] = 0;
  $handler->display->display_options['fields']['text_1']['alter']['trim_whitespace'] = 0;
  $handler->display->display_options['fields']['text_1']['alter']['nl2br'] = 0;
  $handler->display->display_options['fields']['text_1']['alter']['word_boundary'] = 1;
  $handler->display->display_options['fields']['text_1']['alter']['ellipsis'] = 1;
  $handler->display->display_options['fields']['text_1']['alter']['strip_tags'] = 0;
  $handler->display->display_options['fields']['text_1']['alter']['trim'] = 0;
  $handler->display->display_options['fields']['text_1']['alter']['html'] = 0;
  $handler->display->display_options['fields']['text_1']['element_label_colon'] = 1;
  $handler->display->display_options['fields']['text_1']['element_default_classes'] = 1;
  $handler->display->display_options['fields']['text_1']['hide_empty'] = 0;
  $handler->display->display_options['fields']['text_1']['empty_zero'] = 0;
  $handler->display->display_options['fields']['text_1']['hide_alter_empty'] = 1;
  $handler->display->display_options['fields']['text_1']['xpath_selector'] = 'download_link';
  /* Field: name: Text */
  $handler->display->display_options['fields']['text']['id'] = 'text';
  $handler->display->display_options['fields']['text']['table'] = 'xml';
  $handler->display->display_options['fields']['text']['field'] = 'text';
  $handler->display->display_options['fields']['text']['alter']['alter_text'] = 0;
  $handler->display->display_options['fields']['text']['alter']['make_link'] = 1;
  $handler->display->display_options['fields']['text']['alter']['path'] = '[text_1] ';
  $handler->display->display_options['fields']['text']['alter']['absolute'] = 0;
  $handler->display->display_options['fields']['text']['alter']['external'] = 1;
  $handler->display->display_options['fields']['text']['alter']['replace_spaces'] = 0;
  $handler->display->display_options['fields']['text']['alter']['trim_whitespace'] = 0;
  $handler->display->display_options['fields']['text']['alter']['nl2br'] = 0;
  $handler->display->display_options['fields']['text']['alter']['word_boundary'] = 1;
  $handler->display->display_options['fields']['text']['alter']['ellipsis'] = 1;
  $handler->display->display_options['fields']['text']['alter']['strip_tags'] = 0;
  $handler->display->display_options['fields']['text']['alter']['trim'] = 0;
  $handler->display->display_options['fields']['text']['alter']['html'] = 0;
  $handler->display->display_options['fields']['text']['element_label_colon'] = 1;
  $handler->display->display_options['fields']['text']['element_default_classes'] = 1;
  $handler->display->display_options['fields']['text']['hide_empty'] = 0;
  $handler->display->display_options['fields']['text']['empty_zero'] = 0;
  $handler->display->display_options['fields']['text']['hide_alter_empty'] = 1;
  $handler->display->display_options['fields']['text']['xpath_selector'] = 'name';
  /* Field: mdhash: Text */
  $handler->display->display_options['fields']['text_2']['id'] = 'text_2';
  $handler->display->display_options['fields']['text_2']['table'] = 'xml';
  $handler->display->display_options['fields']['text_2']['field'] = 'text';
  $handler->display->display_options['fields']['text_2']['label'] = 'MD5 Hash';
  $handler->display->display_options['fields']['text_2']['alter']['alter_text'] = 0;
  $handler->display->display_options['fields']['text_2']['alter']['make_link'] = 0;
  $handler->display->display_options['fields']['text_2']['alter']['absolute'] = 0;
  $handler->display->display_options['fields']['text_2']['alter']['external'] = 0;
  $handler->display->display_options['fields']['text_2']['alter']['replace_spaces'] = 0;
  $handler->display->display_options['fields']['text_2']['alter']['trim_whitespace'] = 0;
  $handler->display->display_options['fields']['text_2']['alter']['nl2br'] = 0;
  $handler->display->display_options['fields']['text_2']['alter']['word_boundary'] = 1;
  $handler->display->display_options['fields']['text_2']['alter']['ellipsis'] = 1;
  $handler->display->display_options['fields']['text_2']['alter']['strip_tags'] = 0;
  $handler->display->display_options['fields']['text_2']['alter']['trim'] = 0;
  $handler->display->display_options['fields']['text_2']['alter']['html'] = 0;
  $handler->display->display_options['fields']['text_2']['element_label_colon'] = 1;
  $handler->display->display_options['fields']['text_2']['element_default_classes'] = 1;
  $handler->display->display_options['fields']['text_2']['hide_empty'] = 0;
  $handler->display->display_options['fields']['text_2']['empty_zero'] = 0;
  $handler->display->display_options['fields']['text_2']['hide_alter_empty'] = 1;
  $handler->display->display_options['fields']['text_2']['xpath_selector'] = 'mdhash';
  /* Field: version_major: Numeric */
  $handler->display->display_options['fields']['numeric']['id'] = 'numeric';
  $handler->display->display_options['fields']['numeric']['table'] = 'xml';
  $handler->display->display_options['fields']['numeric']['field'] = 'numeric';
  $handler->display->display_options['fields']['numeric']['label'] = 'Major Version';
  $handler->display->display_options['fields']['numeric']['alter']['alter_text'] = 0;
  $handler->display->display_options['fields']['numeric']['alter']['make_link'] = 0;
  $handler->display->display_options['fields']['numeric']['alter']['absolute'] = 0;
  $handler->display->display_options['fields']['numeric']['alter']['external'] = 0;
  $handler->display->display_options['fields']['numeric']['alter']['replace_spaces'] = 0;
  $handler->display->display_options['fields']['numeric']['alter']['trim_whitespace'] = 0;
  $handler->display->display_options['fields']['numeric']['alter']['nl2br'] = 0;
  $handler->display->display_options['fields']['numeric']['alter']['word_boundary'] = 1;
  $handler->display->display_options['fields']['numeric']['alter']['ellipsis'] = 1;
  $handler->display->display_options['fields']['numeric']['alter']['strip_tags'] = 0;
  $handler->display->display_options['fields']['numeric']['alter']['trim'] = 0;
  $handler->display->display_options['fields']['numeric']['alter']['html'] = 0;
  $handler->display->display_options['fields']['numeric']['element_label_colon'] = 1;
  $handler->display->display_options['fields']['numeric']['element_default_classes'] = 1;
  $handler->display->display_options['fields']['numeric']['hide_empty'] = 0;
  $handler->display->display_options['fields']['numeric']['empty_zero'] = 0;
  $handler->display->display_options['fields']['numeric']['hide_alter_empty'] = 1;
  $handler->display->display_options['fields']['numeric']['xpath_selector'] = 'version_major';
  /* Filter criterion: version_major: Numeric */
  $handler->display->display_options['filters']['numeric']['id'] = 'numeric';
  $handler->display->display_options['filters']['numeric']['table'] = 'xml';
  $handler->display->display_options['filters']['numeric']['field'] = 'numeric';
  $handler->display->display_options['filters']['numeric']['exposed'] = TRUE;
  $handler->display->display_options['filters']['numeric']['expose']['operator_id'] = 'numeric_op';
  $handler->display->display_options['filters']['numeric']['expose']['label'] = 'Major Version';
  $handler->display->display_options['filters']['numeric']['expose']['operator'] = 'numeric_op';
  $handler->display->display_options['filters']['numeric']['expose']['identifier'] = 'numeric';
  $handler->display->display_options['filters']['numeric']['expose']['multiple'] = FALSE;
  $handler->display->display_options['filters']['numeric']['xpath_selector'] = 'version_major';

  /* Display: Page */
  $handler = $view->new_display('page', 'Page', 'page');
  $handler->display->display_options['path'] = 'drupal-project-releases';

  $views[$view->name] = $view;

  return $views;
}