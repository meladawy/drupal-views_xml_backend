<?php

/**
 * @file
 * Contains \Drupal\views_xml_backend\Plugin\views\field\XmlMarkup.
 */

namespace Drupal\views_xml_backend\Plugin\views\field;

use Drupal\views_xml_backend\Plugin\views\field\XmlText;
use Drupal\views\ResultRow;
use Drupal\Core\Form\FormStateInterface;

/**
 * A handler to provide an XML markup field.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("xml_markup")
 */
class XmlMarkup extends XmlText {

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['format'] = array('default' => '');
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {

    parent::buildOptionsForm($form, $form_state);
    global $user;
    foreach (filter_formats($user) as $id => $format) {
      $options[$id] = $format->get('name');
    }
    $form['format'] = array(
      '#title' => t('Format'),
      '#description' => t('The filter format'),
      '#type' => 'select',
      '#default_value' => $this->options['format'],
      '#required' => TRUE,
      '#options' => $options,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    $value = $values->{$this->options['id']};
    if ($value) {
      $value = str_replace('<!--break-->', '', $value);
      return check_markup($value, $this->options['format'], '');
    }
  }

}