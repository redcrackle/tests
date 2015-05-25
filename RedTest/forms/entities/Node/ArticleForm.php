<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 11/16/14
 * Time: 11:37 AM
 */

namespace RedTest\forms\entities\Node;

use RedTest\core\forms\entities\Node\NodeForm;

class ArticleForm extends NodeForm {

  public function __construct($nid = NULL) {
    parent::__construct($nid);
  }
}