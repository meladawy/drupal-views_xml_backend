<?php

/**
 * @file
 * Contains \Drupal\views_xml_backend\Plugin\views\argument\Date.
 */

namespace Drupal\views_xml_backend\Plugin\views\argument;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\argument\Date as ViewsDate;
use Drupal\views_xml_backend\AdminLabelTrait;

/**
 * Date XML argument handler.
 *
 * @ingroup views_argument_handlers
 *
 * @ViewsArgument("views_xml_backend_date")
 */
class Date extends ViewsDate implements XmlArgumentInterface {

  use AdminLabelTrait;

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['xpath_selector']['default'] = '';
    $options['granularity']['default'] = 'day';

    return $options;
  }

  /**
   * {@inheritdoc}
    */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    $form['xpath_selector'] = [
      '#type' => 'textfield',
      '#title' => $this->t('XPath selector'),
      '#description' => $this->t('The field name in the table that will be used as the filter.'),
      '#default_value' => $this->options['xpath_selector'],
      '#required' => TRUE,
    ];

    $form['granularity'] = [
      '#type' => 'radios',
      '#title' => $this->t('Granularity'),
      '#options' => [
        'second' => $this->t('Second'),
        'minute' => $this->t('Minute'),
        'hour'   => $this->t('Hour'),
        'day'    => $this->t('Day'),
        'month'  => $this->t('Month'),
        'year'   => $this->t('Year'),
      ],
      '#description' => $this->t('The granularity is the smallest unit to use when determining whether two dates are the same; for example, if the granularity is "Year" then all dates in 1999, regardless of when they fall in 1999, will be considered the same date.'),
      '#default_value' => $this->options['granularity'],
    ];

    parent::buildOptionsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function defaultArgumentForm(&$form, FormStateInterface $form_state) {
    parent::defaultArgumentForm($form, $form_state);
    unset($form['default_argument_type']['#options']['node_changed']);
    unset($form['default_argument_type']['#options']['node_created']);
  }

  /**
   * {@inheritdoc}
   */
  public function query($group_by = FALSE) {
    $this->query->addArgument($this);
  }

  /**
   * {@inheritdoc}
   */
  public function __toString() {
    $xpath = $this->options['xpath_selector'];
    $granularity = $this->options['granularity'];
    $value = views_xml_backend_date($this->getValue(), $granularity);

    return "php:functionString('views_xml_backend_date', $xpath, '$granularity') = $value";
  }

}
