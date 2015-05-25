<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 2/19/15
 * Time: 4:58 PM
 */

namespace RedTest\entities\Node;

use RedTest\core\entities\Node;

class Page extends Node {

  /**
   * Default constructor. This is needed so that other classes can create
   * Test. If we don't have this class, then other classes can't call
   * Node directly because NodeForm's constructor is a protected function.
   *
   * @param int $nid
   *   Node id if an existing node needs to be loaded.
   */
  public function __construct($nid = NULL) {
    parent::__construct($nid);
  }
}