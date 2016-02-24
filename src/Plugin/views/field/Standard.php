<?php

/**
 * @file
 * Contains \Drupal\views_xml_backend\Plugin\views\field\Standard.
 */

namespace Drupal\views_xml_backend\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Drupal\views_xml_backend\Sorter\StringSorter;

/**
 * A handler to provide an XML text field.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("views_xml_backend_standard")
 */
class Standard extends FieldPluginBase {

  use XmlFieldHelperTrait;

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    return parent::defineOptions() + $this->getDefaultXmlOptions();
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    $form = $this->getDefaultXmlOptionsForm($form, $form_state);

    parent::buildOptionsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    return $this->renderXmlRow($this->getXmlListValue($values));
  }

  /**
   * {@inheritdoc}
   */
  public function clickSort($order) {
    $this->query->addSort(new StringSorter($this->realField, $order));
  }

}
