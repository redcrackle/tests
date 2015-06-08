<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 2/19/15
 * Time: 5:00 PM
 */

namespace RedTest\tests\entities\node\article\crud;

use RedTest\core\entities\User;
use RedTest\core\RedTest_Framework_TestCase;
use RedTest\entities\Node\Article;
use RedTest\forms\entities\Node\ArticleForm;
use RedTest\core\Menu;


/**
 * Class SuperUserTest
 *
 * @package RedTest\tests\test\crud
 */
class SuperUserTest extends RedTest_Framework_TestCase {

  /**
   * @var array
   */
  private static $options;

  /**
   * @var int
   */
  private static $articleId;

  /**
   * @var array
   */
  private static $fields;

  /**
   * Log in as uid 1.
   */
  public static function setupBeforeClass() {
    list($success, $userObject, $msg) = User::loginProgrammatically(1);
    self::assertTrue($success, $msg);

    self::$options = array(
      'required_fields_only' => FALSE,
    );
  }

  /**
   * Make sure that superuser has access to create an article.
   */
  public function testCreateAccess() {
    $this->assertTrue(
      Menu::hasAccess('node/add/article'),
      'Superuser does not have access to create an article.'
    );

    $this->assertTrue(
      Article::hasCreateAccess(),
      "Superuser does not have access to create an article."
    );
  }

  /**
   * Make sure that superuser is able to create new articles.
   *
   * @depends testCreateAccess
   */
  public function testCreate() {
    $articleForm = new ArticleForm();

    list($success, $fields, $msg) = $articleForm->fillRandomValues(
      self::$options
    );
    $this->assertTrue($success, $msg);

    list($success, $articleObject, $msg) = $articleForm->submit();
    $this->assertTrue($success, $msg);

    list($success, $msg) = $articleObject->checkValues($fields);
    $this->assertTrue($success, $msg);

    self::$articleId = $articleObject->getId();
  }

  /**
   * Make sure that superuser has access to edit the article.
   *
   * @depends testCreate
   */
  public function testUpdateAccess() {
    $this->assertTrue(
      Menu::hasAccess('node/' . self::$articleId . '/edit'),
      'Superuser does not have access to edit an article.'
    );

    $articleObject = new Article(self::$articleId);
    $this->assertTrue(
      $articleObject->hasUpdateAccess(),
      "Superuser does not have access to edit an article."
    );
  }

  /**
   * Make sure that the superuser is able to update his own article.
   *
   * @depends testUpdateAccess
   */
  public function testUpdate() {
    $articleForm = new ArticleForm(self::$articleId);

    list($success, self::$fields, $msg) = $articleForm->fillRandomValues(
      self::$options
    );
    $this->assertTrue($success, $msg);

    list($success, $articleObject, $msg) = $articleForm->submit();
    $this->assertTrue($success, $msg);

    list($success, $msg) = $articleObject->checkValues(self::$fields);
    $this->assertTrue($success, $msg);

    return $articleObject->getId();
  }

  /**
   * Make sure that superuser has access to view the article.
   *
   * @depends testUpdate
   */
  public function testViewAccess() {
    $this->assertTrue(
      Menu::hasAccess('node/' . self::$articleId),
      'Superuser does not have access to view an article.'
    );

    $articleObject = new Article(self::$articleId);
    $this->assertTrue(
      $articleObject->hasViewAccess(),
      "Superuser does not have access to view an article."
    );
  }

  /**
   * Make sure that superuser is able to view the article.
   *
   * @depends testViewAccess
   */
  public function testView() {
    $article = new Article(self::$articleId);

    $teaser_view = $article->view('teaser');
    $this->assertArrayHasKey('body', $teaser_view, 'Article teaser does not show body field.');
    $this->assertArrayHasKey('field_image', $teaser_view, 'Article teaser does not show image field.');
    $this->assertArrayHasKey('field_tags', $teaser_view, 'Article teaser does not show tags field.');

    $full_view = $article->view('full');
    $this->assertArrayHasKey('body', $full_view, 'Article does not show body field.');
    $this->assertArrayHasKey('field_image', $full_view, 'Article does not show image field.');
    $this->assertArrayHasKey('field_tags', $full_view, 'Article teaser does not show tags field.');
  }

  /**
   * Make sure that superuser has access to delete the article.
   *
   * @depends testView
   */
  public function testDeleteAccess() {
    $this->assertTrue(
      Menu::hasAccess('node/' . self::$articleId . '/delete'),
      'Superuser does not have access to edit an article.'
    );

    $articleObject = new Article(self::$articleId);
    $this->assertTrue(
      $articleObject->hasUpdateAccess(),
      "Superuser does not have access to create an article."
    );
  }

  /**
   * Make sure that superuser is able to delete the article.
   *
   * @depends testDeleteAccess
   */
  public function testDelete() {
    $article = new Article(self::$articleId);

    list($success, $msg) = $article->delete();
    $this->assertTrue($success, $msg);
  }
}