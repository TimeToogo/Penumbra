<?php

namespace Penumbra\Drivers\Base\Relational\Expressions;

/**
 * Expression representing a constant value.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class EscapedValueExpression extends ValueExpression {
    
    public function Traverse(ExpressionWalker $Walker) {
        return $Walker->WalkEscapedValue($this);
    }
}

?>