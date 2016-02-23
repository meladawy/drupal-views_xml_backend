<?php

/**
 * @file
 * Contains \Drupal\views_xml_backend\Plugin\views\field\XmlText.
 */

namespace Drupal\views_xml_backend\Plugin\views\field;

use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\ResultRow;

/**
 * A handler to provide an XML text field.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("xml_text")
 */
class XmlText extends FieldPluginBase {

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

}
