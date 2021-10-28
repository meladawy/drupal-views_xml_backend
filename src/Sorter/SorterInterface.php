<?php

namespace Drupal\views_xml_backend\Sorter;

/**
 * This is the interface used for sorters.
 *
 * This interface isn't checked directly, any callable will work.
 */
interface SorterInterface {

  /**
   * Sorts a views result.
   *
   * @param \Drupal\views\ResultRow[] &$result
   *   The views result.
   */
  public function __invoke(array &$result);

}
