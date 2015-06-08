<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 6/2/15
 * Time: 1:40 PM
 */

namespace RedTest\tests\entities\node\article\comment;


use RedTest\core\Menu;
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
    list($success, $userObject, $msg) = User::loginProgrammatically(1);
    self::assertTrue($success, $msg);

    list($success, self::$articleObject, $msg) = Article::createRandom();
    self::assertTrue($success, $msg);

    $userObject->logout();

    list($success, $userObject, $msg) = User::createRandom();
    self::assertTrue($success, $msg);

    list($success, $userObject, $msg) = User::login(
      $userObject->getNameValues(),
      $userObject->getPasswordValues()
    );
    self::assertTrue($success, $msg);
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
    $this->assertTrue(
      $articleCommentForm->getInitialized(),
      $articleCommentForm->getErrors()
    );

    list($success, $fields, $msg) = $articleCommentForm->fillRandomValues(
      self::$options
    );
    $this->assertTrue($success, $msg);

    list($success, self::$articleCommentObject, $msg) = $articleCommentForm->submit(
    );
    $this->assertTrue($success, $msg);

    list($success, $msg) = self::$articleCommentObject->checkValues($fields);
    $this->assertTrue($success, $msg);
  }

  /**
   * Make sure that authenticated user has permission to view his own comment.
   *
   * @depends testCommentPost
   */
  public function testCommentViewAccess() {
    $this->assertTrue(
      Menu::hasAccess(
        'comment/' . self::$articleCommentObject->getId() . '/view'
      ),
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
    $this->assertFalse(
      Menu::hasAccess(
        'comment/' . self::$articleCommentObject->getId() . '/edit'
      ),
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
    $this->assertTrue(
      Menu::hasAccess(
        'comment/reply/' . self::$articleObject->getId(
        ) . '/' . self::$articleCommentObject->getId()
      ),
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
    $this->assertTrue(
      $articleReplyForm->getInitialized(),
      $articleReplyForm->getErrors()
    );

    list($success, $fields, $msg) = $articleReplyForm->fillRandomValues(
      self::$options
    );
    $this->assertTrue($success, $msg);

    list($success, $articleReplyObject, $msg) = $articleReplyForm->submit();
    $this->assertTrue($success, $msg);

    list($success, $msg) = $articleReplyObject->checkValues($fields);
    $this->assertTrue($success, $msg);
  }

  /**
   * Make sure that authenticated user is not able to delete his own comment.
   *
   * @depends testCommentReply
   */
  public function testCommentDeleteAccess() {
    $this->assertFalse(
      Menu::hasAccess(
        'comment/' . self::$articleCommentObject->getId() . '/delete'
      ),
      "Authenticated user is able to delete his own comment."
    );
  }
}