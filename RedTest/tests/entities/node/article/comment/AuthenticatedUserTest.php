<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 6/2/15
 * Time: 1:40 PM
 */

namespace RedTest\tests\entities\node\article\comment;


use RedTest\core\Menu;
use RedTest\core\Path;
use RedTest\core\RedTest_Framework_TestCase;
use RedTest\entities\Comment\ArticleComment;
use RedTest\entities\Node\Article;
use RedTest\core\entities\User;
use RedTest\forms\entities\Comment\ArticleCommentForm;

/**
 * Class AuthenticatedUserTest
 *
 * @package RedTest\tests\entities\node\article\comment
 */
class AuthenticatedUserTest extends RedTest_Framework_TestCase {

  /**
   * @var Article
   */
  private static $articleObject;

  /**
   * @var ArticleComment
   */
  private static $articleCommentObject;

  /**
   * @var array
   */
  private static $options = array('required_fields_only' => FALSE);

  /**
   * Create an authenticated user and log in as that user. Create an article.
   */
  public static function setupBeforeClass() {
    $userObject = User::loginProgrammatically(1)->verify(get_class());

    self::$articleObject = Article::createRandom()->verify(get_class());

    $userObject->logout();

    $userObject = User::createRandom()->verify(get_class());

    $userObject = User::login(
      $userObject->getNameValues(),
      $userObject->getPasswordValues()
    )->verify(get_class());
  }

  /**
   * Make sure that authenticated user has permission to post comments.
   */
  public function testCommentPostAccess() {
    $this->assertTrue(
      user_access('post comments'),
      "Authenticated user does not have permission to post comments."
    );
  }

  /**
   * Make sure that authenticated user is able to create comments.
   *
   * @depends testCommentPostAccess
   */
  public function testCommentPost() {
    $articleCommentForm = new ArticleCommentForm(
      NULL,
      self::$articleObject->getId()
    );
    $articleCommentForm->verify($this);

    $fields = $articleCommentForm->fillRandomValues(self::$options)->verify(
      $this
    );

    self::$articleCommentObject = $articleCommentForm->submit()->verify($this);

    self::$articleCommentObject->checkValues($fields)->verify($this);
  }

  /**
   * Make sure that authenticated user has permission to view his own comment.
   *
   * @depends testCommentPost
   */
  public function testCommentViewAccess() {
    $path = new Path(
      'comment/' . self::$articleCommentObject->getId() . '/view'
    );
    $this->assertTrue(
      $path->hasAccess(),
      "Authenticated user does not have permission to view his own comment."
    );
    $this->assertTrue(
      user_access('access comments'),
      "Authenticated user does not have permission to view his own comment."
    );
  }

  /**
   * Make sure that authenticated user does not have permission to edit his own
   * comments.
   *
   * @depends testCommentViewAccess
   */
  public function testCommentEditAccess() {
    $path = new Path(
      'comment/' . self::$articleCommentObject->getId() . '/edit'
    );
    $this->assertFalse(
      $path->hasAccess(),
      "Authenticated user has permission to edit his own comment."
    );

    $this->assertFalse(
      self::$articleCommentObject->hasUpdateAccess(),
      "Authenticated user has permission to edit his own comment."
    );
  }

  /**
   * Make sure that authenticated user has permission to reply to his own
   * comment.
   *
   * @depends testCommentEditAccess
   */
  public function testCommentReplyAccess() {
    $path = new Path(
      'comment/reply/' . self::$articleObject->getId(
      ) . '/' . self::$articleCommentObject->getId()
    );
    $this->assertTrue(
      $path->hasAccess(),
      "Authenticated user does not have permission to reply to his own comment."
    );
  }

  /**
   * Make sure that authenticated user is able to reply to his own comment.
   *
   * @depends testCommentReplyAccess
   */
  public function testCommentReply() {
    $articleReplyForm = new ArticleCommentForm(
      NULL,
      self::$articleObject->getId(),
      self::$articleCommentObject->getId()
    );
    $articleReplyForm->verify($this);

    $fields = $articleReplyForm->fillRandomValues(self::$options)->verify(
      $this
    );

    $articleReplyObject = $articleReplyForm->submit()->verify($this);

    $articleReplyObject->checkValues($fields)->verify($this);
  }

  /**
   * Make sure that authenticated user is not able to delete his own comment.
   *
   * @depends testCommentReply
   */
  public function testCommentDeleteAccess() {
    $path = new Path(
      'comment/' . self::$articleCommentObject->getId() . '/delete'
    );
    $this->assertFalse(
      $path->hasAccess(),
      "Authenticated user is able to delete his own comment."
    );
  }
}