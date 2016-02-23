<?php

/**
 * @file
 * Contains \Drupal\views_xml_backend\Plugin\views\argument\Standard.
 */

namespace Drupal\views_xml_backend\Plugin\views\argument;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\argument\ArgumentPluginBase;

/**
 * Default implementation of the base argument plugin.
 *
 * @ingroup views_argument_handlers
 *
 * @ViewsArgument("views_xml_backend_standard")
 */
class Standard extends ArgumentPluginBase implements XmlArgumentInterface {

  /**
   * {@inheritdoc}
   */
  public function query($group_by = FALSE) {
    // @todo: Handle group_by argument.
    $this->query->addArgument($this);
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['xpath_selector'] = ['default' => ''];

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

    parent::buildOptionsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function __toString() {
    $xpath = $this->options['xpath_selector'];
    $value = $this->escapeXpathArgument($this->getValue());

    return "$xpath = $value";
  }

  /**
   * Escapes an XPath string.
   *
   * @param string $argument
   *   The string to escape.
   *
   * @return string
   *   The escaped string.
   */
  protected function escapeXpathArgument($argument) {
    if (strpos($argument, "'") === FALSE) {
      return "'" . $argument . "'";
    }

    if (strpos($argument, '"') === FALSE) {
      return '"' . $argument . '"';
    }

    $string = $argument;
    $parts = [];

    // XPath doesn't provide a way to escape quotes in strings, so we break up
    // the string and return a concat() function call.
    while (TRUE) {
      if (FALSE !== $pos = strpos($string, "'")) {
        $parts[] = sprintf("'%s'", substr($string, 0, $pos));
        $parts[] = "\"'\"";
        $string = substr($string, $pos + 1);
      }
      else {
        $parts[] = "'$string'";
        break;
      }
    }

    return sprintf('concat(%s)', implode($parts, ', '));
  }

}
