<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 11/19/14
 * Time: 9:45 PM
 */

namespace RedTest\forms\entities\TaxonomyTerm;

use RedTest\core\forms\entities\TaxonomyTerm\TaxonomyFormTerm as TaxonomyFormTerm;

class TagsForm extends TaxonomyFormTerm {

  /**
   * Default constructor.
   *
   * @param int $tid
   *   TaxonomyTerm id if an existing term needs to be loaded.
   */
  public function __construct($tid = NULL) {
    parent::__construct($tid);
  }
}