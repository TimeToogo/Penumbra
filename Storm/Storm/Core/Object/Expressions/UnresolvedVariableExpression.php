<?php

namespace Storm\Core\Object\Expressions;

/**
 * Expression representing a resolved value.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class UnresolvedVariableExpression extends Expression {
    private $Name;
    public function __construct($Name) {
        $this->Name = $Name;
    }
    
    public function Traverse(ExpressionWalker $Walker) {
        return $Walker->WalkUnresolvedValue($this);
    }
    
    public function Simplify() {
        return $this;
    }
    
    /**
     * @return mixed The unresolved variable name
     */
    public function GetName() {
        return $this->Name;
    }
}

?>