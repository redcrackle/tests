<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 6/2/15
 * Time: 1:40 PM
 */

namespace RedTest\tests\entities\node\article\comment;


use RedTest\core\entities\Comment;
use RedTest\core\RedTest_Framework_TestCase;
use RedTest\core\entities\User;
use RedTest\entities\Comment\ArticleComment;
use RedTest\entities\Node\Article;
use RedTest\forms\entities\Comment\ArticleCommentForm;
use RedTest\core\Menu;

/**
 * Class AnonymousUserTest
 *
 * @package RedTest\tests\entities\node\article\comment
 */
class AnonymousUserTest extends RedTest_Framework_TestCase {

  //protected static $deleteCreatedEntities = FALSE;

  /**
   * @var Article
   */
  private static $articleObject;

  /**
   * @var ArticleComment
   */
  private static $articleCommentObject;

  /**
   * Make sure that user is anonymous.
   */
  public static function setupBeforeClass() {
    list($success, $userObject, $msg) = User::loginProgrammatically(1);
    self::assertTrue($success, $msg);

    list($success, self::$articleObject, $msg) = Article::createRandom();
    self::assertTrue($success, $msg);

    /**
     * @todo ArticleComment::createDefault doesn't work yet. Need to fix it.
     */
    /*list($success, self::$articleCommentObject, $msg) = ArticleComment::createDefault(
      1,
      array('nid' => self::$articleObject->getId())
    );*/

    $articleCommentForm = new ArticleCommentForm(NULL, self::$articleObject->getId());
    self::assertTrue($articleCommentForm->getInitialized(), $articleCommentForm->getErrors());

    list($success, $fields, $msg) = $articleCommentForm->fillRandomValues();
    self::assertTrue($success, $msg);

    list($success, self::$articleCommentObject, $msg) = $articleCommentForm->submit();
    self::assertTrue($success, $msg);

    list($success, $msg) = self::$articleCommentObject->checkValues($fields);
    self::assertTrue($success, $msg);

    User::logout();
  }

  /**
   * Make sure that anonymous user does not have permission to post comments.
   */
  public function testCommentPostAccess() {
    $this->assertFalse(
      user_access('post comments'),
      "Anonymous user does has permission to post comments."
    );
  }

  /**
   * Make sure that anonymous user has permission to view a comment.
   */
  public function testCommentViewAccess() {
    $this->assertTrue(
      Menu::hasAccess(
        'comment/' . self::$articleCommentObject->getId() . '/view'
      ),
      "Anonymous user does not have permission to view a comment."
    );
    $this->assertTrue(
      user_access('access comments'),
      "Anonymous user does not have permission to view a comment."
    );
  }

  /**
   * Make sure that anonymous user does not have permission to edit a comment.
   */
  public function testCommentEditAccess() {
    $this->assertFalse(
      Menu::hasAccess(
        'comment/' . self::$articleCommentObject->getId() . '/edit'
      ),
      "Anonymous user has permission to edit a comment."
    );

    $this->assertFalse(
      self::$articleCommentObject->hasUpdateAccess(),
      "Anonymous user has permission to edit a comment."
    );
  }

  /**
   * Make sure that anonymous user does not have permission to reply to a
   * comment.
   */
  /*public function testCommentReplyAccess() {
    $this->assertFalse(
      Menu::hasAccess(
        'comment/reply/' . self::$articleObject->getId(
        ) . '/' . self::$articleCommentObject->getId()
      ),
      "Anonymous user has permission to reply to a comment."
    );
  }*/

  /**
   * Make sure that anonymous user is not able to delete a comment.
   */
  public function testCommentDeleteAccess() {
    $this->assertFalse(
      Menu::hasAccess(
        'comment/' . self::$articleCommentObject->getId() . '/delete'
      ),
      "Authenticated user is able to delete a comment."
    );
  }
}