<?php

namespace Penumbra\Pinq\Expressions;

use \Penumbra\Core\Object\Expressions;

class AggregateVariableExpression extends Expressions\Expression {
    public function __construct() {
        
    }
    
    public function Simplify() {
        return $this;
    }

    public function Traverse(Expressions\ExpressionWalker $Walker) {
        return $this;
    }
}

?>