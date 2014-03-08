<?php

namespace Storm\Core\Object\Expressions;

/**
 * Placeholder expression for traversing the entity
 */
class EntityExpression extends Expression {
    
    public function Traverse(ExpressionWalker $Walker) {
        return $Walker->WalkEntity($this);
    }
    
    public function Simplify() {
        return $this;
    }
}

?>