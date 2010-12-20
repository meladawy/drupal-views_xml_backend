<?php
// $Id$

/**
 * Implementation of hook_views_default_views().
 */
function xml_views_views_default_views() {
  /*
   * View 'drush_make_releases'
   */
  $view = new view;
  $view->name = 'drush_make_releases';
  $view->description = '';
  $view->tag = '';
  $view->base_table = 'xml';
  $view->human_name = '';
  $view->api_version = '3.0-alpha1';
  $view->disabled = TRUE; /* Edit this to true to make a default view disabled initially */

  /* Display: Defaults */
  $handler = $view->new_display('default', 'Defaults', 'default');
  $handler->display->display_options['access']['type'] = 'none';
  $handler->display->display_options['cache']['type'] = 'none';
  $handler->display->display_options['query']['type'] = 'views_query';
  $handler->display->display_options['query']['options']['xml_file'] = 'http://updates.drupal.org/release-history/drush_make/6.x';
  $handler->display->display_options['query']['options']['row_xpath'] = '/project/releases/release';
  $handler->display->display_options['exposed_form']['type'] = 'basic';
  $handler->display->display_options['pager']['type'] = 'full';
  $handler->display->display_options['style_plugin'] = 'table';
  /* Field: Download URL */
  $handler->display->display_options['fields']['column']['id'] = 'column';
  $handler->display->display_options['fields']['column']['table'] = 'xml';
  $handler->display->display_options['fields']['column']['field'] = 'column';
  $handler->display->display_options['fields']['column']['ui_name'] = 'Download URL';
  $handler->display->display_options['fields']['column']['label'] = 'Download URL';
  $handler->display->display_options['fields']['column']['exclude'] = TRUE;
  $handler->display->display_options['fields']['column']['alter']['alter_text'] = 0;
  $handler->display->display_options['fields']['column']['alter']['make_link'] = 0;
  $handler->display->display_options['fields']['column']['alter']['absolute'] = 0;
  $handler->display->display_options['fields']['column']['alter']['trim'] = 0;
  $handler->display->display_options['fields']['column']['alter']['word_boundary'] = 1;
  $handler->display->display_options['fields']['column']['alter']['ellipsis'] = 1;
  $handler->display->display_options['fields']['column']['alter']['strip_tags'] = 0;
  $handler->display->display_options['fields']['column']['alter']['html'] = 0;
  $handler->display->display_options['fields']['column']['element_label_colon'] = 1;
  $handler->display->display_options['fields']['column']['element_default_classes'] = 1;
  $handler->display->display_options['fields']['column']['hide_empty'] = 0;
  $handler->display->display_options['fields']['column']['empty_zero'] = 0;
  $handler->display->display_options['fields']['column']['xpath_selector'] = 'download_link';
  /* Field: Name */
  $handler->display->display_options['fields']['column_1']['id'] = 'column_1';
  $handler->display->display_options['fields']['column_1']['table'] = 'xml';
  $handler->display->display_options['fields']['column_1']['field'] = 'column';
  $handler->display->display_options['fields']['column_1']['ui_name'] = 'Name';
  $handler->display->display_options['fields']['column_1']['label'] = 'Name';
  $handler->display->display_options['fields']['column_1']['alter']['alter_text'] = 0;
  $handler->display->display_options['fields']['column_1']['alter']['make_link'] = 1;
  $handler->display->display_options['fields']['column_1']['alter']['path'] = '[column]';
  $handler->display->display_options['fields']['column_1']['alter']['absolute'] = 0;
  $handler->display->display_options['fields']['column_1']['alter']['trim'] = 0;
  $handler->display->display_options['fields']['column_1']['alter']['word_boundary'] = 1;
  $handler->display->display_options['fields']['column_1']['alter']['ellipsis'] = 1;
  $handler->display->display_options['fields']['column_1']['alter']['strip_tags'] = 0;
  $handler->display->display_options['fields']['column_1']['alter']['html'] = 0;
  $handler->display->display_options['fields']['column_1']['element_label_colon'] = 1;
  $handler->display->display_options['fields']['column_1']['element_default_classes'] = 1;
  $handler->display->display_options['fields']['column_1']['hide_empty'] = 0;
  $handler->display->display_options['fields']['column_1']['empty_zero'] = 0;
  $handler->display->display_options['fields']['column_1']['xpath_selector'] = 'name';
  /* Field: MD5 Hash */
  $handler->display->display_options['fields']['column_2']['id'] = 'column_2';
  $handler->display->display_options['fields']['column_2']['table'] = 'xml';
  $handler->display->display_options['fields']['column_2']['field'] = 'column';
  $handler->display->display_options['fields']['column_2']['ui_name'] = 'MD5 Hash';
  $handler->display->display_options['fields']['column_2']['label'] = 'MD5 Hash';
  $handler->display->display_options['fields']['column_2']['alter']['alter_text'] = 0;
  $handler->display->display_options['fields']['column_2']['alter']['make_link'] = 0;
  $handler->display->display_options['fields']['column_2']['alter']['absolute'] = 0;
  $handler->display->display_options['fields']['column_2']['alter']['trim'] = 0;
  $handler->display->display_options['fields']['column_2']['alter']['word_boundary'] = 1;
  $handler->display->display_options['fields']['column_2']['alter']['ellipsis'] = 1;
  $handler->display->display_options['fields']['column_2']['alter']['strip_tags'] = 0;
  $handler->display->display_options['fields']['column_2']['alter']['html'] = 0;
  $handler->display->display_options['fields']['column_2']['element_label_colon'] = 1;
  $handler->display->display_options['fields']['column_2']['element_default_classes'] = 1;
  $handler->display->display_options['fields']['column_2']['hide_empty'] = 0;
  $handler->display->display_options['fields']['column_2']['empty_zero'] = 0;
  $handler->display->display_options['fields']['column_2']['xpath_selector'] = 'mdhash';
  /* Filter: XML: Column */
  $handler->display->display_options['filters']['column']['id'] = 'column';
  $handler->display->display_options['filters']['column']['table'] = 'xml';
  $handler->display->display_options['filters']['column']['field'] = 'column';
  $handler->display->display_options['filters']['column']['exposed'] = TRUE;
  $handler->display->display_options['filters']['column']['expose']['operator'] = 'column_op';
  $handler->display->display_options['filters']['column']['expose']['label'] = 'Major version';
  $handler->display->display_options['filters']['column']['expose']['identifier'] = 'version_major';
  $handler->display->display_options['filters']['column']['expose']['single'] = TRUE;
  $handler->display->display_options['filters']['column']['xpath_selector'] = 'version_major';

  /* Display: Page */
  $handler = $view->new_display('page', 'Page', 'page_1');
  $handler->display->display_options['path'] = 'drush-make-releases';
  $translatables['drush_make_releases'] = array(
    t('Defaults'),
    t('more'),
    t('Apply'),
    t('Reset'),
    t('Sort By'),
    t('Asc'),
    t('Desc'),
    t('Items per page'),
    t('- All -'),
    t('Offset'),
    t('Download URL'),
    t('Name'),
    t('[column]'),
    t('MD5 Hash'),
    t('Major version'),
    t('Page'),
  );
  $views[$view->name] = $view;

  return $views;
}


