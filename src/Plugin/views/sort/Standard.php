<?php

/**
 * @file
 * Contains \Drupal\views_xml_backend\Plugin\views\sort\Standard.
 */

namespace Drupal\views_xml_backend\Plugin\views\sort;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\sort\SortPluginBase;
use Drupal\views\ResultRow;
use Drupal\views\ViewExecutable;

/**
 * Default implementation of the base sort plugin.
 *
 * @ingroup views_sort_handlers
 *
 * @ViewsSort("views_xml_backend_standard")
 */
class Standard extends SortPluginBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $this->query->addSort('sort_' . $this->realField, $this->options['xpath_selector'], $this);
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
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    $form['xpath_selector'] = [
      '#type' => 'textfield',
      '#title' => 'XPath selector',
      '#description' => $this->t('The field name in the table that will be used for the sort.'),
      '#default_value' => $this->options['xpath_selector'],
      '#required' => TRUE,
    ];

    parent::buildOptionsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function __invoke(array &$result) {
    $alias = 'sort_' . $this->realField;

    if ($this->options['order'] === 'ASC') {
      uasort($result, function (ResultRow $a, ResultRow $b) use ($alias) {
        return strcasecmp(reset($a->$alias), reset($b->$alias));
      });
    }

    else {
      uasort($result, function (ResultRow $a, ResultRow $b) use ($alias) {
        return strcasecmp(reset($b->$alias), reset($a->$alias));
      });
    }
  }

}
