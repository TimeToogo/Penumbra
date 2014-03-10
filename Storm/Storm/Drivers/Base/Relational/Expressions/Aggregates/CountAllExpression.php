<?php

namespace Storm\Core\Object\Expressions\Aggregates;

use \Storm\Core\Object\Expressions\Expression;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class CountAllExpression extends AggregateExpression {
    public function __construct() {
        parent::__construct();
    }
    
    public function Simplify() {
        return $this;
    }
}

?>