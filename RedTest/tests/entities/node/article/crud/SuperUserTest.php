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
    $userObject = User::loginProgrammatically(1)->verify(get_class());

    self::$options = array(
      'required_fields_only' => FALSE,
    );
  }

  /**
   * Make sure that superuser has access to create an article.
   */
  public function testCreateAccess() {
    $path = new Path('node/add/article');
    $this->assertTrue(
      $path->hasAccess(),
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
    $articleForm->verify($this);

    $fields = $articleForm->fillRandomValues(self::$options)->verify($this);

    $articleObject = $articleForm->submit()->verify($this);

    $articleObject->checkValues($fields)->verify($this);

    self::$articleId = $articleObject->getId();
  }

  /**
   * Make sure that superuser has access to edit the article.
   *
   * @depends testCreate
   */
  public function testUpdateAccess() {
    $path = new Path('node/' . self::$articleId . '/edit');
    $this->assertTrue(
      $path->hasAccess(),
      'Superuser does not have access to edit an article.'
    );

    $articleObject = new Article(self::$articleId);
    $articleObject->verify($this);
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
    $articleForm->verify($this);

    self::$fields = $articleForm->fillRandomValues(self::$options)->verify(
      $this
    );

    $articleObject = $articleForm->submit()->verify($this);

    $articleObject->checkValues(self::$fields)->verify($this);

    return $articleObject->getId();
  }

  /**
   * Make sure that superuser has access to view the article.
   *
   * @depends testUpdate
   */
  public function testViewAccess() {
    $path = new Path('node/' . self::$articleId);
    $this->assertTrue(
      $path->hasAccess(),
      'Superuser does not have access to view an article.'
    );

    $articleObject = new Article(self::$articleId);
    $articleObject->verify($this);
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
    $article->verify($this);

    $teaser_view = $article->view('teaser');
    $this->assertArrayHasKey(
      'body',
      $teaser_view,
      'Article teaser does not show body field.'
    );
    $this->assertArrayHasKey(
      'field_image',
      $teaser_view,
      'Article teaser does not show image field.'
    );
    $this->assertArrayHasKey(
      'field_tags',
      $teaser_view,
      'Article teaser does not show tags field.'
    );

    $full_view = $article->view('full');
    $this->assertArrayHasKey(
      'body',
      $full_view,
      'Article does not show body field.'
    );
    $this->assertArrayHasKey(
      'field_image',
      $full_view,
      'Article does not show image field.'
    );
    $this->assertArrayHasKey(
      'field_tags',
      $full_view,
      'Article teaser does not show tags field.'
    );
  }

  /**
   * Make sure that superuser has access to delete the article.
   *
   * @depends testView
   */
  public function testDeleteAccess() {
    $path = new Path('node/' . self::$articleId . '/delete');
    $this->assertTrue(
      $path->hasAccess(),
      'Superuser does not have access to edit an article.'
    );

    $articleObject = new Article(self::$articleId);
    $articleObject->verify($this);
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
    $article->verify($this);

    $article->delete()->verify($this);
  }
}