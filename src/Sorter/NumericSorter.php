<?php

/**
 * @file
 * Contains \Drupal\views_xml_backend\Sorter\NumericSorter.
 */

namespace Drupal\views_xml_backend\Sorter;

use Drupal\views\ResultRow;

/**
 * Provides sorting for numbers.
 */
class NumericSorter extends StringSorter {

  /**
   * {@inheritdoc}
   */
  public function __invoke(array &$result) {
    // Notice the order of the subtraction.

    switch ($this->direction) {
      case 'ASC':
        uasort($result, function (ResultRow $a, ResultRow $b) {
          return reset($a->{$this->field}) - reset($b->{$this->field});
        });
        break;

      case 'DESC':
        uasort($result, function (ResultRow $a, ResultRow $b) {
          return reset($b->{$this->field}) - reset($a->{$this->field});
        });
        break;
    }
  }

}
