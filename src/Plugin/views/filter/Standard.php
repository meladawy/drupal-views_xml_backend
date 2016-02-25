<?php

/**
 * @file
 * Contains \Drupal\views_xml_backend\Plugin\views\filter\Standard.
 */

namespace Drupal\views_xml_backend\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\filter\StringFilter;
use Drupal\views_xml_backend\Xpath;

/**
 * Default implementation of the base filter plugin.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("views_xml_backend_standard")
 */
class Standard extends StringFilter {

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

    // @todo Add case sensitive option.
    $options['case']['default'] = TRUE;
    $options['xpath_selector']['default'] = '';

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function operators() {
    $operators = [
      '=' => [
        'title' => $this->t('Is equal to'),
        'short' => $this->t('='),
      ],
      '!=' => [
        'title' => $this->t('Is not equal to'),
        'short' => $this->t('!='),
      ],
      'contains' => [
        'title' => $this->t('Contains'),
        'short' => $this->t('contains'),
      ],
      '!contains' => [
        'title' => $this->t('Does not contain'),
        'short' => $this->t('not_contains'),
      ],
      'starts-with' => [
        'title' => $this->t('Starts with'),
        'short' => $this->t('begins'),
      ],
      '!starts-with' => [
        'title' => $this->t('Does not start with'),
        'short' => $this->t('not_begins'),
      ],
      'ends-with' => [
        'title' => $this->t('Ends with'),
        'short' => $this->t('ends'),
      ],
      '!ends-with' => [
        'title' => $this->t('Does not end with'),
        'short' => $this->t('not_ends'),
      ],
    ];

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
    $value = Xpath::escapeXpathString($this->value);

    if ($operator === '=' || $operator === '!=') {
      return "$xpath $operator $value";
    }

    if (strpos($operator, '!') === 0) {
      $operator = ltrim($operator, '!');
      return "not($operator($xpath, $value))";
    }

    return "$operator($xpath, $value)";
  }

}