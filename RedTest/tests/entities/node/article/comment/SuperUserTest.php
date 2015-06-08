<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 6/2/15
 * Time: 1:40 PM
 */

namespace RedTest\tests\entities\node\article\comment;


use RedTest\core\forms\entities\Comment\CommentConfirmDelete;
use RedTest\core\RedTest_Framework_TestCase;
use RedTest\entities\Comment\ArticleComment;
use RedTest\entities\Node\Article;
use RedTest\core\entities\User;
use RedTest\forms\entities\Comment\ArticleCommentForm;
use RedTest\core\Menu;

/**
 * Class SuperUserTest
 *
 * @package RedTest\tests\entities\node\article\comment
 */
class SuperUserTest extends RedTest_Framework_TestCase {

  /**
   * @var Article
   */
  private static $articleObject;

  /**
   * @var ArticleComment
   */
  private static $articleCommentObject;

  /**
   * @var ArticleComment
   */
  private static $articleReplyObject;

  /**
   * @var array
   */
  private static $options = array('required_fields_only' => FALSE);

  /**
   * Log in as uid 1 and create an article.
   */
  public static function setupBeforeClass() {
    list($success, $userObject, $msg) = User::loginProgrammatically(1);
    self::assertTrue($success, $msg);

    list($success, self::$articleObject, $msg) = Article::createRandom();
    self::assertTrue($success, $msg);
  }

  /**
   * Make sure that comments are open on the article object.
   */
  public function testCommentOpen() {
    $this->assertEquals(
      COMMENT_NODE_OPEN,
      self::$articleObject->getEntity()->comment,
      'Comments are not open on the article.'
    );
  }

  /**
   * Make sure that superuser has permission to post comments.
   */
  public function testCommentPostAccess() {
    $this->assertTrue(
      user_access('post comments'),
      "Superuser does not have permission to post comments."
    );
  }

  /**
   * Make sure that superuser is able to create comments.
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
   * Make sure that superuser has permission to view his own comment.
   *
   * @depends testCommentPost
   */
  public function testCommentViewAccess() {
    $this->assertTrue(
      Menu::hasAccess(
        'comment/' . self::$articleCommentObject->getId() . '/view'
      ),
      "Superuser does not have permission to view his own comment."
    );
    $this->assertTrue(
      user_access('access comments'),
      "Superuser does not have permission to view his own comment."
    );
  }

  /**
   * Make sure that superuser has permission to edit his own comments.
   *
   * @depends testCommentViewAccess
   */
  public function testCommentEditAccess() {
    $this->assertTrue(
      Menu::hasAccess(
        'comment/' . self::$articleCommentObject->getId() . '/edit'
      ),
      "Superuser does not have permission to edit his own comment."
    );

    $this->assertTrue(
      self::$articleCommentObject->hasUpdateAccess(),
      "Superuser does not have permission to edit his own comment."
    );
  }

  /**
   * Make sure that superuser is able to edit comments.
   *
   * @depends testCommentEditAccess
   */
  public function testCommentEdit() {
    $articleCommentForm = new ArticleCommentForm(
      self::$articleCommentObject->getId(),
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
   * Make sure that superuser has permission to reply to his own comment.
   *
   * @depends testCommentEdit
   */
  public function testCommentReplyAccess() {
    $this->assertTrue(
      Menu::hasAccess(
        'comment/reply/' . self::$articleObject->getId(
        ) . '/' . self::$articleCommentObject->getId()
      ),
      "Superuser does not have permission to reply to his own comment."
    );
  }

  /**
   * Make sure that superuser is able to reply to his own comment.
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

    list($success, self::$articleReplyObject, $msg) = $articleReplyForm->submit(
    );
    $this->assertTrue($success, $msg);

    list($success, $msg) = self::$articleReplyObject->checkValues($fields);
    $this->assertTrue($success, $msg);
  }

  /**
   * Make sure that superuser has permission to delete his own comment.
   *
   * @depends testCommentReply
   */
  public function testCommentDeleteAccess() {
    $this->assertTrue(
      Menu::hasAccess(
        'comment/' . self::$articleCommentObject->getId() . '/delete'
      ),
      "Superuser is not able to delete his own comment."
    );
  }

  /**
   * Make sure that superuser is able to delete his own comment.
   */
  public function testCommentDelete() {
    $commentDeleteForm = new CommentConfirmDelete(
      self::$articleCommentObject->getId()
    );
    list($success, $msg) = $commentDeleteForm->submit();

    $articleCommentObject = new ArticleComment(
      self::$articleCommentObject->getId()
    );
    $this->assertFalse(
      $articleCommentObject->getInitialized(),
      "Superuser is not able to delete his own comment."
    );
  }
}