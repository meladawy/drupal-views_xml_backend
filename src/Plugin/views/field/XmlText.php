<?php

/**
 * @file
 * Contains \Drupal\views_xml_backend\Plugin\views\field\XmlText.
 */

namespace Drupal\views_xml_backend\Plugin\views\field;

use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\ResultRow;
use Drupal\Component\Utility\SafeMarkup;

/**
 * A handler to provide an XML text field.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("xml_text")
 */
class XmlText extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function usesGroupBy() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    // do nothing -- to override the parent query.
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['xpath_selector'] = array('default' => '');
    $options['multiple'] = array('default' => FALSE);
    $options['list_type'] = array('default' => 'ul');
    $options['custom_separator'] = array('default' => ', ');

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    $form['xpath_selector'] = array(
      '#title' => t('XPath selector'),
      '#description' => t('The xpath selector'),
      '#type' => 'textfield',
      '#default_value' => $this->options['xpath_selector'],
      '#required' => TRUE,
    );
    $form['multiple'] = array(
      '#title' => t('Allow multiple'),
      '#description' => t('Treat this field as multi-valued.'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['multiple'],
    );
    $form['list_options'] = array(
      '#type' => 'fieldset',
      '#states' => array(
        'visible' => array(
          ':input[name="options[multiple]"]' => array(
            'checked' => TRUE,
          ),
        ),
      ),
    );
    $form['list_type'] = array(
      '#title' => t('List type'),
      '#description' => t('The type of list.'),
      '#type' => 'radios',
      '#default_value' => $this->options['list_type'],
      '#options' => array(
        'ul' => t('Unordered list'),
        'ol' => t('Ordered list'),
//      'br' => t('HTML break'),
        'other' => t('Custom separator'),
      ),
      '#fieldset' => 'list_options',
    );
    $form['custom_separator'] = array(
      '#type' => 'textfield',
      '#title' => t('Separator'),
      '#default_value' => $this->options['custom_separator'],
      '#states' => array(
        'visible' => array(
          ':input[name="options[list_type]"]' => array(
            'value' => 'other',
          ),
        ),
      ),
      '#fieldset' => 'list_options',
    );

    parent::buildOptionsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {

    $output = '';

    if ($this->options['multiple']) {
      if ($this->options['list_type'] == 'other') {
        $output = $this->sanitizeValue(implode(SafeMarkup::checkPlain($this->options['custom_separator']), $values->{$this->options['id']}));
      }
//    elseif($this->options['list_type'] == 'br') {
//      $output = $this->sanitizeValue(implode('<br />', $values->{$this->options['id']}));
//    }
      else {
        $output = array(
          '#theme' => 'item_list',
          '#items' => $values->{$this->options['id']},
          '#list_type' => $this->options['list_type'],
        );
      }
    }
    else {
      $output = $this->sanitizeValue($values->{$this->options['id']});
    }

    return $output;
  }
}
