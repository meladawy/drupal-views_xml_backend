<?php

/**
 * @file
 * Contains \Drupal\views_xml_backend\Plugin\views\field\Date.
 */

namespace Drupal\views_xml_backend\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\field\Date as ViewsDate;
use Drupal\views\ResultRow;
use Drupal\views_xml_backend\Sorter\StringSorter;

/**
 * A handler to provide an XML date field.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("views_xml_backend_date")
 */
class Date extends ViewsDate {

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
  public function render(ResultRow $row) {
    $values = $this->getXmlListValue($row);

    $output_values = [];

    foreach ($values as $value) {
      $row->{$this->field_alias} = $value;
      $output_values[] = parent::render($row);
    }

    return $this->renderXmlRow($output_values);
  }

  /**
   * {@inheritdoc}
   */
  public function clickSort($order) {
    $this->query->addSort(new StringSorter($this->realField, $order));
  }

}
