<?php

/**
 * @file
 * Contains \Drupal\Tests\views_xml_backend\Unit\Plugin\views\field\MarkupTest.
 */

namespace Drupal\Tests\views_xml_backend\Unit\Plugin\views\field;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Tests\views_xml_backend\Unit\ViewsXmlBackendTestBase;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\ViewExecutable;
use Drupal\views_xml_backend\Plugin\views\field\Markup;

/**
 * @coversDefaultClass \Drupal\views_xml_backend\Plugin\views\field\Markup
 * @group views_xml_backend
 */
class MarkupTest extends ViewsXmlBackendTestBase {

  /**
   * @covers ::render_item
   */
  public function testRenderItem() {
    $account = $this->prophesize(AccountProxyInterface::class);

    $plugin = new Markup([], '', [], $account->reveal());

    $options = ['format' => 'my_format'];

    $plugin->init($this->getMockedView(), $this->getMockedDisplay(), $options);

    $result = $plugin->render_item(0, ['value' => 'foo']);

    $this->assertSame('foo', $result['#text']);
    $this->assertSame('my_format', $result['#format']);
  }

}
