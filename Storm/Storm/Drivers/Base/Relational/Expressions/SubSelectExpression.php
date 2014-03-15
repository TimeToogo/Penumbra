<?php

namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Core\Relational\Select;

class SubSelectExpression extends Expression {
    private $Select;
    
    public function __construct(Select $Select) {
        $this->Select = $Select;
    }
    
    public function Traverse(ExpressionWalker $Walker) {
        return $Walker->WalkSubSelect($this);
    }
    
    /**
     * @return Select
     */
    public function GetSelect() {
        return $this->CastType;
    }
    
    /**
     * @return self
     */
    public function Update(Select $Select) {
        if($this->Select === $Select) {
            return $this;
        }
        
        return new self($Select);
    }
}

?>