<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 11/16/14
 * Time: 11:37 AM
 */

namespace RedTest\entities\Node;

use RedTest\core\entities\Node as Node;

class Article extends Node {

  /**
   * Default constructor.
   *
   * @param int $nid
   *   Node id if an existing node needs to be loaded.
   */
  public function __construct($nid = NULL) {
    parent::__construct($nid);
  }
} 