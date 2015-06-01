<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 2/19/15
 * Time: 5:00 PM
 */

namespace RedTest\tests\taxonomy_term\crud;

use RedTest\core\entities\User;
use RedTest\core\Utils;
use RedTest\core\Menu;
use RedTest\entities\TaxonomyTerm\Tags;
use RedTest\forms\entities\TaxonomyTerm\TagsForm;

/**
 * Drupal root directory.
 */
if (!defined('DRUPAL_ROOT')) {
  define('DRUPAL_ROOT', getcwd());
}
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
// We need to provide a non-empty SERVER_SOFTWARE so that execution doesn't get
// treated as command-line execution by drupal_is_cli() function. If it is
// treated as command-line execution, then drupal_session_start() doesn't invoke
// session_start(). As a result, session_destroy() in User::logout() function
// throws an error. Although this does not affect RedTest execution or even
// session handling, it's better to not let Drupal throw this error in the first
// place.
if (empty($_SERVER['SERVER_SOFTWARE'])) {
  drupal_override_server_variables(array('SERVER_SOFTWARE' => 'RedTest'));
}
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

class SuperUserTest extends \PHPUnit_Framework_TestCase {

  /**
   * @var array
   */
  protected $backupGlobalsBlacklist = array(
    'user',
    'entities',
    'language',
    'language_url',
    'language_content'
  );

  /**
   * @var User
   */
  private static $userObject;

  /**
   * @var Tags
   */
  private static $tagsObject;

  /**
   * @var array
   */
  private static $fields;

  public static function setupBeforeClass() {
    list($success, self::$userObject, $msg) = User::loginProgrammatically(1);
    self::assertTrue($success, $msg);
  }

  public function testTagsCreateAccess() {
    $this->assertEquals(
      'drupal_get_form',
      Menu::getPageCallback('admin/structure/taxonomy/tags/add'),
      "Page callback to add a Tags taxonomy term is incorrect."
    );

    $pageArguments = Menu::getPageArguments(
      'admin/structure/taxonomy/tags/add'
    );
    $pageArgument = array_shift($pageArguments);
    $this->assertEquals(
      'taxonomy_form_term',
      $pageArgument,
      "Page arguments to add a Tags taxonomy term are incorrect."
    );

    $this->assertTrue(
      Menu::hasAccess('admin/structure/taxonomy/tags/add'),
      "Superuser does not have access to create a Tags taxonomy term."
    );
  }

  /**
   * Check that tag creation works.
   *
   * @depends testTagsCreateAccess
   */
  public function testTagsCreate() {
    $tagsForm = new TagsForm();

    list($success, self::$fields, $msg) = $tagsForm->fillDefaultValues(
      array('required_fields_only' => FALSE)
    );
    $this->assertTrue($success, $msg);

    list($success, self::$tagsObject, $msg) = $tagsForm->submit();
    $this->assertTrue($success, $msg);

    list($success, $msg) = self::$tagsObject->checkValues(self::$fields);
    $this->assertTrue($success, $msg);
  }

  /**
   * Check that user has access to edit a tag.
   *
   * @depends testTagsCreate
   */
  public function testTagsUpdateAccess() {
    $tid = self::$tagsObject->getId();
    $path = "taxonomy/term/$tid/edit";

    $this->assertEquals(
      'drupal_get_form',
      Menu::getPageCallback($path),
      "Page callback to edit a Tags taxonomy term is incorrect."
    );

    $pageArguments = Menu::getPageArguments($path);
    $pageArgument = array_shift($pageArguments);
    $this->assertEquals(
      'taxonomy_form_term',
      $pageArgument,
      "Page arguments to edit a Tags taxonomy term are incorrect."
    );

    $this->assertTrue(
      Menu::hasAccess($path),
      "Superuser does not have access to edit a Tags taxonomy term."
    );
  }

  /**
   * Check that user can edit a tag.
   *
   * @depends testTagsUpdateAccess
   */
  public function testTagsUpdate() {
    $tid = self::$tagsObject->getId();

    $tagsForm = new TagsForm($tid);

    // First check that saving a form without editing any field works.
    list($success, self::$tagsObject, $msg) = $tagsForm->submit();
    $this->assertTrue($success, $msg);

    list($success, $msg) = self::$tagsObject->checkValues(self::$fields);
    $this->assertTrue($success, $msg);

    list($success, self::$fields, $msg) = $tagsForm->fillDefaultValues(
      array('required_fields_only' => FALSE)
    );
    $this->assertTrue($success, $msg);

    list($success, self::$tagsObject, $msg) = $tagsForm->submit();
    $this->assertTrue($success, $msg);

    list($success, $msg) = self::$tagsObject->checkValues(self::$fields);
    $this->assertTrue($success, $msg);
  }

  /**
   * Check that user has access to delete a tag.
   *
   * @depends testTagsCreate
   */
  public function testTagsDelete() {
    $tid = self::$tagsObject->getId();

    $tagsForm = new TagsForm($tid);

    // First check that saving a form without editing any field works.
    list($success, $msg) = $tagsForm->delete();
    $this->assertTrue($success, $msg);
  }

  public static function tearDownAfterClass() {
    self::$userObject->logout();
    Utils::deleteCreatedEntities();
  }
}