<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 11/19/14
 * Time: 10:55 PM
 */

namespace RedTest\entities\TaxonomyTerm;

use RedTest\core\entities\TaxonomyTerm;

class Tags extends TaxonomyTerm {

  /**
   * Default constructor.
   *
   * @param int $tid
   *   Node id if an existing node needs to be loaded.
   */
  public function __construct($tid = NULL) {
    parent::__construct($tid);
  }
}