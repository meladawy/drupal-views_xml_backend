<?php

namespace Drupal\views_xml_backend\Tests;

/**
 * Tests filtering functions from the Views XML Backend module.
 *
 * @group views_xml_backend
 */
class ViewsXMLBackendFilteringTest extends ViewsXMLBackendBase {

  /**
   * Tests Views XML Backend View filtering.
   */
  public function testFilteringViewsXMLBackend() {
    $this->addStandardXMLBackendView();
    $this->drupalGet("admin/structure/views/nojs/add-handler/{$this->viewsXMLBackendViewId}/default/filter");

    // Check add filtering ability.
    $this->submitForm(['name[views_xml_backend.text]' => 'views_xml_backend.text'], t('Add and configure @handler', ['@handler' => t('filter criteria')]));
    // @todo Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
    // Change assertion to buttonExists() if checking for a button.
    $this->assertSession()->fieldExists('options[xpath_selector]', "The XML input 'options[xpath_selector]' was found");
    $fields = [
      'options[xpath_selector]' => 'version_major',
      'options[operator]' => '!=',
      'options[value]' => '3',
    ];
    $this->drupalGet(NULL);
    $this->submitForm($fields, t('Apply'));

    $this->drupalGet("admin/structure/views/nojs/handler/{$this->viewsXMLBackendViewId}/default/filter/text");
    $this->assertFieldByXPath("//input[@id='edit-options-xpath-selector']", 'version_major', "Value 'version_major' found in field 'edit-options-xpath-selector'");
    $this->assertSession()->checkboxChecked('edit-options-operator---2');
    $this->assertFieldByXPath("//input[@id='edit-options-value']", '3', "Value '3' found in field 'edit-options-value'");
  }

}
