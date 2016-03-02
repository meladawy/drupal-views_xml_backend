<?php

/**
 * @file
 * Contains \Drupal\views_xml_backend\Plugin\views\argument\Numeric.
 */

namespace Drupal\views_xml_backend\Plugin\views\argument;

/**
 * Numeric XML argument handler.
 *
 * @ingroup views_argument_handlers
 *
 * @ViewsArgument("views_xml_backend_numeric")
 */
class Numeric extends Standard {

  /**
   * {@inheritdoc}
   */
  public function __toString() {
    return $this->options['xpath_selector'] . '=' . $this->getValue();
  }

}
