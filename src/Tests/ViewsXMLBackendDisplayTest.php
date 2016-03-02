<?php

/**
 * @file
 * Contains \Drupal\views_xml_backend\Tests\ViewsXMLBackendDisplayTest.
 */

namespace Drupal\views_xml_backend\Tests;

/**
 * Tests basic functions from the Views XML Backend module.
 *
 * @group views_xml_backend
 */

use Drupal\simpletest\WebTestBase;
use Drupal\Core\Url;
use Drupal\views\Views;
use Drupal\views\Entity\View;
use Drupal\simpletest\AssertContentTrait;

class ViewsXMLBackendDisplayTest extends WebTestBase {

  protected $strictConfigSchema = FALSE;

  /**
   * Modules to enable for this test.
   *
   * @var string[]
   */
  public static $modules = [
    'views',
    'views_ui',
    'views_xml_backend',
    'node',
    'block',
    'taxonomy'
  ];

  /**
   * The administrator account to use for the tests.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  private $viewsXMLBackendUser;

  /**
   * Views content type option.
   *
   * @var string
   */
  private $viewsXMLBackendSelector;


  /**
   * Views XML Backend base view title.
   *
   * @var string
   */
  private $viewsXMLBackendTitle;


  /**
   * Views XML Backend base view xml file.
   *
   * @var string
   */
  private $viewsXMLBackendFile;

  /**
   * Views XML Backend base view id.
   *
   * @var string
   */
  private $viewsXMLBackendViewId;

  /**
   * Views XML Backend base view admin add path.
   *
   * @var string
   */
  private $viewsXMLBackendViewAddPath;

  /**
   * Views XML Backend base view admin edit path.
   *
   * @var string
   */
  private $viewsXMLBackendViewEditPath;

  /**
   * Views XML Backend base view admin query path.
   *
   * @var string
   */
  private $viewsXMLBackendViewQueryPath;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->viewsXMLBackendFile = 'https://updates.drupal.org/release-history/views/7.x';
    $this->viewsXMLBackendSelector = (string) 'standard:views_xml_backend';
    $permissions = [
      'administer users',
      'administer permissions',
      'administer views',
      'access user profiles',
      'administer permissions',
      'administer blocks',
      'bypass node access',
      'view all revisions',
    ];
    $this->viewsXMLBackendUser = $this->createTestUser($permissions);
    $this->drupalLogin($this->viewsXMLBackendUser);
  }

  /**
   * Creates a valid test User with supplied permissions.
   *
   * @param array $permissions
   *   Permissions user should have.
   *
   * @return \Drupal\Core\Session\AccountInterface|false
   *   A user account interface or FALSE if fails
   */
  private function createTestUser(array $permissions = []) {
    return $this->drupalCreateUser($permissions);
  }


  /**
   * Tests Views XML Backend option appears in new View admin page.
   */
  public function testOptionViewViewsXMLBackend() {

    // Setup consistent test variables to use throughout new test View.
    $this->viewsXMLBackendViewId = strtolower($this->randomMachineName(16));
    $this->viewsXMLBackendTitle = $this->randomMachineName(16);

    $this->viewsXMLBackendViewAddPath = '/admin/structure/views/add';
    $this->viewsXMLBackendViewEditPath = "/admin/structure/views/view/{$this->viewsXMLBackendViewId}/edit/default";
    $this->viewsXMLBackendViewQueryPath = "admin/structure/views/nojs/display/{$this->viewsXMLBackendViewId}/default/query";

    // Create a new test View.
    $this->drupalGet($this->viewsXMLBackendViewAddPath);

    // Confirm Views XML Backend option is found.
    $this->assertOption('edit-show-wizard-key', 'standard:views_xml_backend', "The XML select option 'standard:views_xml_backend' was found in 'edit-show-wizard-key'");

  }

  /**
   * Tests new Views XML Backend View can be created.
   */
  public function testAddViewViewsXMLBackend() {

    /*
     * NOTE: To save a test view $strictConfigSchema must be set to FALSE.
     * @see https://www.drupal.org/node/2679725
     */

    // Setup consistent test variables to use throughout new test View.
    $this->viewsXMLBackendViewId = strtolower($this->randomMachineName(16));
    $this->viewsXMLBackendTitle = $this->randomMachineName(16);

    $this->viewsXMLBackendViewAddPath = '/admin/structure/views/add';
    $this->viewsXMLBackendViewEditPath = "/admin/structure/views/view/{$this->viewsXMLBackendViewId}/edit/default";
    $this->viewsXMLBackendViewQueryPath = "admin/structure/views/nojs/display/{$this->viewsXMLBackendViewId}/default/query";

    $default = [
      'show[wizard_key]' => 'standard:views_xml_backend',
    ];
    $this->drupalPostAjaxForm($this->viewsXMLBackendViewAddPath, $default, 'show[wizard_key]');

    // Confirm standard:views_xml_backend was selected in show[wizard_key] select
    $new_id = $this->xpath("//*[starts-with(@id, 'edit-show-wizard-key')]/@id");
    $new_wizard_id = (string) $new_id[0]['id'];
    $this->assertOptionSelected($new_wizard_id, 'standard:views_xml_backend', "The XML select option 'standard:views_xml_backend' was selected on {$new_wizard_id}");

    // Save the new test View.
    $default = [
      'label' => $this->viewsXMLBackendTitle,
      'id' => $this->viewsXMLBackendViewId,
      'description' => $this->randomMachineName(16),
      'show[wizard_key]' => 'standard:views_xml_backend',
    ];
    $this->drupalPostForm($this->viewsXMLBackendViewAddPath, $default, t('Save and edit'));
    // Confirm new view is saved.
    $this->assertText("The view {$this->viewsXMLBackendTitle} has been saved");

  }

  /**
   * Tests Views XML Backend View Query Settings XML source option can be set.
   */
  public function testSetXMLSourceViewViewsXMLBackend() {

    // Setup consistent test variables to use throughout new test View.
    $this->viewsXMLBackendViewId = strtolower($this->randomMachineName(16));
    $this->viewsXMLBackendTitle = $this->randomMachineName(16);

    $this->viewsXMLBackendViewAddPath = '/admin/structure/views/add';
    $this->viewsXMLBackendViewEditPath = "/admin/structure/views/view/{$this->viewsXMLBackendViewId}/edit/default";
    $this->viewsXMLBackendViewQueryPath = "admin/structure/views/nojs/display/{$this->viewsXMLBackendViewId}/default/query";

    $default = [
      'show[wizard_key]' => 'standard:views_xml_backend',
    ];
    $this->drupalPostAjaxForm($this->viewsXMLBackendViewAddPath, $default, 'show[wizard_key]');

    // Confirm standard:views_xml_backend was selected in show[wizard_key] select
    $new_id = $this->xpath("//*[starts-with(@id, 'edit-show-wizard-key')]/@id");
    $new_wizard_id = (string) $new_id[0]['id'];
    $this->assertOptionSelected($new_wizard_id, 'standard:views_xml_backend', "The XML select option 'standard:views_xml_backend' was selected on {$new_wizard_id}");

    // Save the new test View.
    $default = [
      'label' => $this->viewsXMLBackendTitle,
      'id' => $this->viewsXMLBackendViewId,
      'description' => $this->randomMachineName(16),
      'show[wizard_key]' => 'standard:views_xml_backend',
    ];
    $this->drupalPostForm($this->viewsXMLBackendViewAddPath, $default, t('Save and edit'));
    // Confirm new view is saved.
    $this->assertText("The view {$this->viewsXMLBackendTitle} has been saved");

    // Update the Query settings on the new View to use an XML file as source.
    $this->drupalGet($this->viewsXMLBackendViewQueryPath);
    $this->assertField('query[options][xml_file]', "The XML select option 'query[options][xml_file]' was found");
    $this->assertField('query[options][row_xpath]', "The XML select option 'query[options][row_xpath]' was found");
    $xml_setting = [
      'query[options][xml_file]' => $this->viewsXMLBackendFile,
      'query[options][row_xpath]' => "/project/releases/release"
    ];
    $this->drupalPostForm($this->viewsXMLBackendViewQueryPath, $xml_setting, t('Apply'));
    $this->drupalPostForm($this->viewsXMLBackendViewEditPath, array(), t('Save'));

    // Check that the Query Settings are saved into the view itself.
    $view = Views::getView($this->viewsXMLBackendViewId);
    $view->initDisplay();
    $view->initQuery();
    $this->assertEqual($this->viewsXMLBackendFile, $view->query->options['xml_file'], 'Query settings got saved');

  }

  /**
   * Provides a list of routes to test.
   *
   * @param string $route_type
   *   Key for a route.
   *
   * @return string|false
   *   A route string or FALSE if not found
   */
  private function getPageRoutes($route_type) {
    $routes = [
      'add_view' => 'views_ui.add',
    ];
    if (array_key_exists($route_type, $routes)) {
      return $routes[$route_type];
    }
    return FALSE;
  }


  private function assertOptionbyName($name, $option, $message = '', $group = 'Browser') {
    $options = $this->xpath('//select[@name=:name]//option[@value=:option]', array(
      ':name' => $name,
      ':option' => $option
    ));
    return $this->assertTrue(isset($options[0]), $message ? $message : SafeMarkup::format('Option @option for field @id exists.', array(
      '@option' => $option,
      '@id' => $id
    )), $group);
  }


}
