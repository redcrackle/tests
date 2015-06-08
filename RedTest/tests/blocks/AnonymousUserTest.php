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
 * Class AnonymousUserTest
 *
 * @package RedTest\tests\blocks
 */
class AnonymousUserTest extends RedTest_Framework_TestCase {

  /**
   * Log the user out. This should not really be needed but just to be sure.
   */
  public static function setUpBeforeClass() {
    User::logout();
  }

  /**
   * Make sure that the login block is not present on homepage or /a path.
   */
  public function testLoginBlock() {
    $block = new Block('user_login');
    $this->assertTrue(
      $block->isPresent('a'),
      "User login block is not present on /a path."
    );
    $this->assertTrue(
      $block->isPresent('<front>'),
      "User login block is not present on the homepage."
    );
  }

  /**
   * Make sure that navigation menu is not present on homepage or /a path.
   */
  public function testNavigationBlock() {
    $block = new Block('system_navigation');
    $this->assertTrue(
      $block->isNotPresent('a'),
      "Navigation block is present on /a path."
    );
    $this->assertTrue(
      $block->isNotPresent('<front>'),
      "Navigation block is not present on the homepage."
    );
  }

  /**
   * Make sure that search form block is not present on homepage or on /a path.
   */
  public function testSearchFormBlock() {
    $block = new Block('search_form');
    $this->assertTrue(
      $block->isNotPresent('a'),
      'Search form block is present on /a path.'
    );
    $this->assertTrue(
      $block->isNotPresent('<front>'),
      'Search form block is present on the homepage.'
    );
  }
}