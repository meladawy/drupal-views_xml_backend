<?php

namespace Drupal\views_xml_backend\Tests;

/**
 * Tests sorting functions from the Views XML Backend module.
 *
 * @group views_xml_backend
 */
class ViewsXMLBackendSortingTest extends ViewsXMLBackendBase {

  /**
   * Tests Views XML Backend View sorting.
   */
  public function testSortingViewsXMLBackend() {
    $this->addStandardXMLBackendView();
    $this->drupalGet("admin/structure/views/nojs/add-handler/{$this->viewsXMLBackendViewId}/default/sort");

    // Check add sorting ability.
    $this->submitForm(['name[views_xml_backend.text]' => 'views_xml_backend.text'], t('Add and configure @handler', ['@handler' => t('sort criteria')]));
    // @todo Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
    // Change assertion to buttonExists() if checking for a button.
    $this->assertSession()->fieldExists('options[xpath_selector]', "The XML input 'options[xpath_selector]' was found");
    $fields = [
      'options[xpath_selector]' => 'download_link',
      'options[order]' => 'DESC',
    ];
    $this->drupalGet(NULL);
    $this->submitForm($fields, t('Apply'));

    $this->drupalGet("admin/structure/views/nojs/handler/{$this->viewsXMLBackendViewId}/default/sort/text");
    $this->assertFieldByXPath("//input[@id='edit-options-xpath-selector']", 'download_link', "Value 'download_link' found in field 'edit-options-xpath-selector'");
    $field_id = $this->xpath("//*[starts-with(@id, 'edit-options-order-desc')]/@id");
    $new_field_id = (string) $field_id[0]['id'];
    $this->assertFieldByXPath("//input[@id='{$new_field_id}']", 'DESC', "Value 'DESC' found in field {$new_field_id}");

  }

}
