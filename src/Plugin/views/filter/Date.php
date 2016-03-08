<?php

/**
 * @file
 * Contains \Drupal\views_xml_backend\Plugin\views\filter\Date.
 */

namespace Drupal\views_xml_backend\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\filter\Date as ViewsDate;
use Drupal\views_xml_backend\AdminLabelTrait;

/**
 * Date filter implementation.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("views_xml_backend_date")
 */
class Date extends ViewsDate implements XmlFilterInterface {

  use AdminLabelTrait;

  /**
   * {@inheritdoc}
   */
  public function query() {
    $this->query->addFilter($this);
  }

  /**
   * {@inheritdoc}
   */
  public function defineOptions() {
    $options = parent::defineOptions();

    $options['xpath_selector']['default'] = '';

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function operators() {
    $operators = parent::operators();

    unset($operators['regular_expression']);

    return $operators;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    $form['xpath_selector'] = [
      '#type' => 'textfield',
      '#title' => 'XPath selector',
      '#description' => $this->t('The field name in the table that will be used as the filter.'),
      '#default_value' => $this->options['xpath_selector'],
      '#required' => TRUE,
    ];

    parent::buildOptionsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function __toString() {
    $operator = $this->operator;
    $xpath = $this->options['xpath_selector'];

    $min = views_xml_backend_date($this->value['min']);
    $max = views_xml_backend_date($this->value['max']);

    if ($operator === 'between') {
      return "php:functionString('views_xml_backend_date', $xpath) >= $min and php:functionString('views_xml_backend_date', $xpath) <= $max";
    }

    if ($operator === 'not between') {
      return "php:functionString('views_xml_backend_date', $xpath) <= $min and php:functionString('views_xml_backend_date', $xpath) >= $max";
    }

    $value = views_xml_backend_date($this->value['value']);

    return "php:functionString('views_xml_backend_date', $xpath) $operator $value";
  }

}
