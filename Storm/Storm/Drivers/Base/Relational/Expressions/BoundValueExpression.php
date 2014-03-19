<?php

namespace Storm\Drivers\Base\Relational\Expressions;

/**
 * Expression representing a constant value.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class BoundValueExpression extends ValueExpression {
    
    public function Traverse(ExpressionWalker $Walker) {
        return $Walker->WalkBoundValue($this);
    }
}

?>