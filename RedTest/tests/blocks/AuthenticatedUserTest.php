<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 5/24/15
 * Time: 2:52 PM
 */

namespace RedTest\tests\blocks;

use RedTest\core\Block;
use RedTest\core\entities\User;
use RedTest\core\RedTest_Framework_TestCase;


/**
 * Class AuthenticatedUserTest
 *
 * @package RedTest\tests\blocks
 */
class AuthenticatedUserTest extends RedTest_Framework_TestCase {

  /**
   * Create an authenticated user and log in as that user.
   */
  public static function setUpBeforeClass() {
    $userObject = User::createRandom()->verify(get_class());

    $userObject = User::login(
      $userObject->getNameValues(),
      $userObject->getPasswordValues()
    )->verify(get_class());
  }

  /**
   * Make sure that login block is not present on either homepage or /a path.
   */
  public function testLoginBlock() {
    $block = new Block('user_login');
    $this->assertTrue(
      $block->isNotPresent('a'),
      "User login block is present on /a path."
    );
    $this->assertTrue(
      $block->isNotPresent('<front>'),
      "User login block is present on the homepage."
    );
  }

  /**
   * Make sure that navigation menu is not present on either homepage or /a
   * path.
   */
  public function testNavigationBlock() {
    $block = new Block('system_navigation');
    $this->assertTrue(
      $block->isNotPresent('a'),
      "Navigation block is present on /a path."
    );
    $this->assertTrue(
      $block->isNotPresent('<front>'),
      "Navigation block is present on the homepage."
    );
  }
}