<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 2/19/15
 * Time: 5:00 PM
 */

namespace RedTest\tests\entities\node\article\crud;

use RedTest\core\entities\User;
use RedTest\core\Path;
use RedTest\core\RedTest_Framework_TestCase;
use RedTest\entities\Node\Article;
use RedTest\core\Menu;


/**
 * Class AuthenticatedUserTest
 *
 * @package RedTest\tests\test\crud
 */
class AuthenticatedUserTest extends RedTest_Framework_TestCase {

  /**
   * @var User
   */
  private static $userObject;

  /**
   * @var array
   */
  private static $options;

  /**
   * Create an authenticated user and log in as that user.
   */
  public static function setupBeforeClass() {
    self::$options = array(
      'required_fields_only' => FALSE,
    );

    User::logout();

    $userObject = User::createRandom()->verify(get_class());

    self::$userObject = User::login(
      $userObject->getNameValues(),
      $userObject->getPasswordValues()
    )->verify(get_class());
  }

  /**
   * Make sure that the authenticated user has access to create an article.
   */
  public function testCreateAccess() {
    $path = new Path('node/add/article');
    $this->assertFalse(
      $path->hasAccess(),
      'Authenticated user has access to create an article.'
    );

    $this->assertFalse(
      Article::hasCreateAccess(),
      "Authenticated user has access to create an article."
    );
  }

  /**
   * Make sure that authenticated user is able to create new articles.
   *
   * @depends testCreateAccess
   */
  /*public function testCreate() {
    $articleForm = new ArticleForm();

    list($success, $fields, $msg) = $articleForm->fillDefaultValues(
      self::$options
    );
    $this->assertTrue($success, $msg);

    list($success, $articleObject, $msg) = $articleForm->submit();
    $this->assertTrue($success, $msg);

    list($success, $msg) = $articleObject->checkValues($fields);
    $this->assertTrue($success, $msg);
  }*/

  /*public function testAllDefault() {
    $this->assertEquals(
      'node_add',
      Menu::getPageCallback('node/add/test'),
      "Page callback to add a Test node is incorrect."
    );

    $this->assertTrue(
      Test::hasCreateAccess(),
      "Authenticated user does not have access to create a Test node."
    );

    list($success, $tagsObjects, $msg) = Tags::createDefault(5);
    $this->assertTrue($success, $msg);

    $testForm = new TestForm();

    $options = array(
      'required_fields_only' => FALSE,
      'references' => array(
        'taxonomy_terms' => array(
          'tags' => $tagsObjects,
        ),
      ),
    );

    list($success, $fields, $msg) = $testForm->fillDefaultValues(
      $options
    );
    $this->assertTrue($success, $msg);

    list($success, $nodeObject, $msg) = $testForm->submit();
    $this->assertTrue($success, $msg);

    list($success, $msg) = $nodeObject->checkValues($fields);
    $this->assertTrue($success, $msg);

    $testForm = new TestForm($nodeObject->getId());

    list($success, $nodeObject, $msg) = $testForm->submit();
    $this->assertTrue($success, $msg);

    list($success, $msg) = $nodeObject->checkValues($fields);
    $this->assertTrue($success, $msg);

    $testForm = new TestForm($nodeObject->getId());

    list($success, $fields, $msg) = $testForm->fillDefaultValues(
      $options
    );
    $this->assertTrue($success, $msg);

    list($success, $nodeObject, $msg) = $testForm->submit();
    $this->assertTrue($success, $msg);

    list($success, $msg) = $nodeObject->checkValues($fields);
    $this->assertTrue($success, $msg);

    $this->assertTrue(
      $nodeObject->hasViewAccess(),
      "Authenticated user does not have access to view a Test node."
    );

    $this->assertTrue(
      $nodeObject->hasUpdateAccess(),
      "Authenticated user does not have access to update a Test node."
    );

    $this->assertTrue(
      $nodeObject->hasDeleteAccess(),
      "Authenticated user does not have access to delete a Test node."
    );
  }*/
}