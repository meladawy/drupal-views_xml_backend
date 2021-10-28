<?php

namespace Drupal\Tests\views_xml_backend\Unit;

use Drupal\views_xml_backend\Xpath;

/**
 * @coversDefaultClass \Drupal\views_xml_backend\Xpath
 * @group views_xml_backend
 */
class XpathTest extends ViewsXmlBackendTestBase {

  /**
   * @covers ::escapeXpathString
   */
  public function testEscapeXpathString() {
    $this->assertSame("'foo'", Xpath::escapeXpathString('foo'));
    $this->assertSame('"fo\'o"', Xpath::escapeXpathString("fo'o"));

    // The complex bits.
    $this->assertSame('concat(\'"foo\', "\'", \'bar"\')', Xpath::escapeXpathString('"foo\'bar"'));
  }

}
