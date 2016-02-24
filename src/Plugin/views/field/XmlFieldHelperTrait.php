<?php

/**
 * @file
 * Contains \Drupal\views_xml_backend\Plugin\views\field\XmlFieldHelperTrait.
 */

namespace Drupal\views_xml_backend\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\ResultRow;
use Drupal\Component\Utility\SafeMarkup;

/**
 * A handler to provide an XML text field.
 */
trait XmlFieldHelperTrait {

  /**
   * {@inheritdoc}
   */
  public function usesGroupBy() {
    return FALSE;
  }

  /**
   * Called to add the field to a query.
   */
  public function query() {
    $this->field_alias = $this->options['id'];
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultXmlOptions() {
    $options = [];

    $options['xpath_selector']['default'] = '';
    $options['multiple']['default'] = FALSE;
    $options['list_type']['default'] = 'ul';
    $options['custom_separator']['default'] = ', ';

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultXmlOptionsForm(array $form, FormStateInterface $form_state) {
    $form['xpath_selector'] = [
      '#title' => $this->t('XPath selector'),
      '#description' => $this->t('The xpath selector'),
      '#type' => 'textfield',
      '#default_value' => $this->options['xpath_selector'],
      '#required' => TRUE,
    ];

    $form['multiple'] = [
      '#title' => $this->t('Allow multiple'),
      '#description' => $this->t('Treat this field as multi-valued.'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['multiple'],
    ];

    $form['list_type'] = [
      '#title' => $this->t('List type'),
      '#description' => $this->t('The type of list.'),
      '#type' => 'radios',
      '#default_value' => $this->options['list_type'],
      '#options' => [
        'ul' => $this->t('Unordered list'),
        'ol' => $this->t('Ordered list'),
        'br' => $this->t('HTML break'),
        'other' => $this->t('Custom separator'),
      ],
      '#states' => [
        'visible' => [
          ':input[name="options[multiple]"]' => [
            'checked' => TRUE,
          ],
        ],
      ],
    ];

    $form['custom_separator'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Separator'),
      '#default_value' => $this->options['custom_separator'],
      '#states' => [
        'visible' => [
          ':input[name="options[list_type]"]' => ['value' => 'other'],
          ':input[name="options[multiple]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function renderXmlRow(array $values) {
    if (empty($this->options['multiple'])) {
      return $this->sanitizeValue(reset($values));
    }

    if ($this->options['list_type'] === 'other') {
      return $this->sanitizeValue(implode(SafeMarkup::checkPlain($this->options['custom_separator']), $values));
    }

    if ($this->options['list_type'] === 'br') {
      return $this->sanitizeValue(implode('<br>', $values));
    }

    return [
      '#theme' => 'item_list',
      '#items' => $values,
      '#list_type' => $this->options['list_type'],
    ];
  }

}
