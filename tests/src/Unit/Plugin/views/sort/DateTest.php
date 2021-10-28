<?php

namespace Drupal\Tests\views_xml_backend\Unit\Plugin\views\sort;

use Prophecy\PhpUnit\ProphecyTrait;
use Drupal\Tests\views_xml_backend\Unit\ViewsXmlBackendTestBase;
use Drupal\views_xml_backend\Plugin\views\query\Xml;
use Drupal\views_xml_backend\Plugin\views\sort\Date;
use Drupal\views_xml_backend\Sorter\DateSorter;
use Prophecy\Argument;

/**
 * @coversDefaultClass \Drupal\views_xml_backend\Plugin\views\sort\Date
 * @group views_xml_backend
 */
class DateTest extends ViewsXmlBackendTestBase {

  use ProphecyTrait;

  /**
   * @covers ::query
   * @doesNotPerformAssertions
   */
  public function testRenderItem() {
    $plugin = new Date([], '', []);

    $options = ['id' => 'sorter_id', 'xpath_selector' => 'xpath_query'];

    $plugin->init($this->getMockedView(), $this->getMockedDisplay(), $options);

    $query = $this->prophesize(Xml::class);
    $query->addField('sort_date_sorter_id', 'xpath_query')->shouldBeCalled();
    $query->addSort(Argument::type(DateSorter::class))->shouldBeCalled();

    $plugin->query = $query->reveal();

    $plugin->query();
  }

}
