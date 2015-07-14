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
 * Class SuperUserTest
 *
 * @package RedTest\tests\blocks
 */
class SuperUserTest extends RedTest_Framework_TestCase {

  /**
   * Log in as uid 1.
   */
  public static function setUpBeforeClass() {
    $userObject = User::loginProgrammatically(1)->verify(get_class());
  }

  /**
   * Make sure that login block is not present on homepage and on path /a.
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
   * Make sure that navigation menu is present on homepage and on /a path.
   */
  public function testNavigationBlock() {
    $block = new Block('system_navigation');
    $this->assertTrue(
      $block->isNotPresent('a'),
      "Navigation block is present on /a path."
    );
    $this->assertTrue(
      $block->isPresent('<front>'),
      "Navigation block is not present on the homepage."
    );
  }

  /**
   * Make sure that search form block is present on homepage and on /a path.
   */
  public function testSearchFormBlock() {
    $block = new Block('search_form');
    $this->assertTrue(
      $block->isPresent('a'),
      'Search form block is not present on /a path.'
    );
    $this->assertTrue(
      $block->isPresent('<front>'),
      'Search form block is not present on the homepage.'
    );
  }
}