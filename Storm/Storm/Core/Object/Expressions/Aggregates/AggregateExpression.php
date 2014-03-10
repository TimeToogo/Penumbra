<?php

namespace Storm\Core\Object\Expressions\Aggregates;

use \Storm\Core\Object\Expressions\Expression;
use \Storm\Core\Object\Expressions\ExpressionWalker;

/**
 * Expression for an aggregate.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class AggregateExpression extends Expression {
    final public function Traverse(ExpressionWalker $Walker) {
        $Walker->WalkAggregate($this);
    }
}

?>