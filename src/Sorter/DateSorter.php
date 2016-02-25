<?php

/**
 * @file
 * Contains \Drupal\views_xml_backend\Sorter\DateSorter.
 */

namespace Drupal\views_xml_backend\Sorter;

use Drupal\views\ResultRow;

/**
 * Provides sorting for dates.
 */
class DateSorter extends StringSorter {

  /**
   * {@inheritdoc}
   */
  public function __invoke(array &$result) {
    // Notice the order of the subtraction.

    switch ($this->direction) {
      case 'ASC':
        uasort($result, function (ResultRow $a, ResultRow $b) {
          $a_value = $this->convertToUnixTimestamp(reset($a->{$this->field}));
          $b_value = $this->convertToUnixTimestamp(reset($b->{$this->field}));

          return $a_value - $b_value;
        });
        break;

      case 'DESC':
        uasort($result, function (ResultRow $a, ResultRow $b) {
          $a_value = $this->convertToUnixTimestamp(reset($a->{$this->field}));
          $b_value = $this->convertToUnixTimestamp(reset($b->{$this->field}));

          return $b_value - $a_value;
        });
        break;
    }
  }

  /**
   * Coverts a value to a UNIX timestamp.
   *
   * @param string|int $date
   *   The date to convert.
   *
   * @return int
   *   The unix timestamp of the date.
   */
  protected function convertToUnixTimestamp($date) {
    if (is_numeric($date)) {
      return (int) $date;
    }

    return strtotime($date);
  }

}
