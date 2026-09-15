<?php

namespace Drupal\Tests\bluecadet_public_files\Functional;

use Drupal\Core\Url;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Tests\BrowserTestBase;

/**
 * Test the Bluecadet Public Files report page.
 *
 * @group bluecadet_public_files
 */
class PublicFilesTest extends BrowserTestBase {

  use StringTranslationTrait;

  /**
   * The modules to load to run the test.
   *
   * @var array
   */
  protected static $modules = [
    'node',
    'bluecadet_public_files',
  ];

  /**
   * Default theme.
   *
   * @var string
   */
  protected $defaultTheme = 'claro';

  /**
   * A user with administration rights.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * An authenticated user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $authenticatedUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp() : void {
    parent::setUp();

    $this->adminUser = $this->drupalCreateUser([
      'access administration pages',
    ]);
    $this->authenticatedUser = $this->drupalCreateUser([]);

  }

  /**
   * Test Basic Functionality.
   */
  public function testBasicFunc() {
    $session = $this->assertSession();

    // Goto Admin Page.
    $config_url = Url::fromRoute('bluecadet_public_files.view_report', [])->toString();
    $this->drupalGet($config_url);
    $session->statusCodeEquals(403);

    // Login Admin User.
    $this->drupalLogin($this->adminUser);

    $this->drupalGet($config_url);

    $session->statusCodeEquals(200);

  }

}
