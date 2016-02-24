<?php

/**
 * @file
 * Contains \Drupal\views_xml_backend\Sorter\StringSorter.
 */

namespace Drupal\views_xml_backend\Sorter;

use Drupal\views\ResultRow;

/**
 * Provides sorting for strings.
 */
class StringSorter implements SorterInterface {

  /**
   * The direction to sort.
   *
   * @var string
   */
  protected $direction;

  /**
   * The field of the result to sort.
   *
   * @var string
   */
  protected $field;

  /**
   * Constructs a StringSorter object.
   *
   * @param string $field
   *   The field to sort.
   * @param string $direction
   *   The direction to sort.
   */
  public function __construct($field, $direction) {
    $this->field = $field;
    $this->direction = strtoupper($direction);
  }

  /**
   * {@inheritdoc}
   */
  public function __invoke(array &$result) {
    // Notice the order of the arguments to strcasecmp().

    switch ($this->direction) {
      case 'ASC':
        uasort($result, function (ResultRow $a, ResultRow $b) {
          return strcasecmp(reset($a->{$this->field}), reset($b->{$this->field}));
        });
        break;

      case 'DESC':
        uasort($result, function (ResultRow $a, ResultRow $b) {
          return strcasecmp(reset($b->{$this->field}), reset($a->{$this->field}));
        });
        break;
    }
  }

}
