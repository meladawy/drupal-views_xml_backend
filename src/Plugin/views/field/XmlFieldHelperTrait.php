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
    $options['type']['default'] = 'separator';
    $options['separator']['default'] = ', ';

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

    $form['type'] = [
      '#type' => 'radios',
      '#title' => $this->t('Display type'),
      '#options' => [
        'ul' => $this->t('Unordered list'),
        'ol' => $this->t('Ordered list'),
        'separator' => $this->t('Simple separator'),
      ],
      '#default_value' => $this->options['type'],
    ];

    $form['separator'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Separator'),
      '#default_value' => $this->options['separator'],
      '#states' => [
        'visible' => [
          ':input[name="options[type]"]' => ['value' => 'separator'],
        ],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function renderItems($items) {
    if (!empty($items)) {
      if ($this->options['type'] == 'separator') {
        $render = [
          '#type' => 'inline_template',
          '#template' => '{{ items|safe_join(separator) }}',
          '#context' => [
            'items' => $items,
            'separator' => $this->sanitizeValue($this->options['separator'], 'xss_admin')
          ]
        ];
      }
      else {
        $render = array(
          '#theme' => 'item_list',
          '#items' => $items,
          '#title' => NULL,
          '#list_type' => $this->options['type'],
        );
      }
      return drupal_render($render);
    }
  }

  public function getItems(ResultRow $row) {
    $return = [];
    if ($values = $this->getValue($row)) {
      foreach ($values as $value) {
        $return[] = ['value' => $value];
      }
    }

    return $return;
  }

  public function render_item($count, $item) {
    return $item['value'];
  }

}
