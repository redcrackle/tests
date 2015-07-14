<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 6/2/15
 * Time: 1:40 PM
 */

namespace RedTest\tests\entities\node\article\comment;


use RedTest\core\forms\entities\Comment\CommentConfirmDelete;
use RedTest\core\Path;
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
    $userObject = User::loginProgrammatically(1)->verify(get_class());

    self::$articleObject = Article::createRandom()->verify(get_class());
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
    $articleCommentForm->verify($this);

    $fields = $articleCommentForm->fillRandomValues(self::$options)->verify(
      $this
    );

    self::$articleCommentObject = $articleCommentForm->submit()->verify($this);

    self::$articleCommentObject->checkValues($fields)->verify($this);
  }

  /**
   * Make sure that superuser has permission to view his own comment.
   *
   * @depends testCommentPost
   */
  public function testCommentViewAccess() {
    $path = new Path(
      'comment/' . self::$articleCommentObject->getId() . '/view'
    );
    $this->assertTrue(
      $path->hasAccess(),
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
    $path = new Path(
      'comment/' . self::$articleCommentObject->getId() . '/edit'
    );
    $this->assertTrue(
      $path->hasAccess(),
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
    $articleCommentForm->verify($this);

    $fields = $articleCommentForm->fillRandomValues(self::$options)->verify($this);

    self::$articleCommentObject = $articleCommentForm->submit()->verify($this);

    self::$articleCommentObject->checkValues($fields)->verify($this);
  }

  /**
   * Make sure that superuser has permission to reply to his own comment.
   *
   * @depends testCommentEdit
   */
  public function testCommentReplyAccess() {
    $path = new Path(
      'comment/reply/' . self::$articleObject->getId(
      ) . '/' . self::$articleCommentObject->getId()
    );
    $this->assertTrue(
      $path->hasAccess(),
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
    $articleReplyForm->verify($this);

    $fields = $articleReplyForm->fillRandomValues(self::$options)->verify($this);

    self::$articleReplyObject = $articleReplyForm->submit()->verify($this);

    self::$articleReplyObject->checkValues($fields)->verify($this);
  }

  /**
   * Make sure that superuser has permission to delete his own comment.
   *
   * @depends testCommentReply
   */
  public function testCommentDeleteAccess() {
    $path = new Path(
      'comment/' . self::$articleCommentObject->getId() . '/delete'
    );
    $this->assertTrue(
      $path->hasAccess(),
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
    $commentDeleteForm->submit()->verify($this);

    $articleCommentObject = new ArticleComment(
      self::$articleCommentObject->getId()
    );
    $this->assertFalse(
      $articleCommentObject->getInitialized(),
      "Superuser is not able to delete his own comment."
    );
  }
}