<?php

namespace Penumbra\Pinq\Expressions;

use \Penumbra\Core\Object\Expressions;

/**
 * Placeholder expression for traversing the entity
 */
class EntityVariableExpression extends Expressions\Expression {
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