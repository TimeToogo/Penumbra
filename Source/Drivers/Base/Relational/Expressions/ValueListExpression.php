<?php

namespace Penumbra\Drivers\Base\Relational\Expressions;

class ValueListExpression extends Expression {
    private $ValueExpressions;
    public function __construct(array $ValueExpressions) {
        $this->ValueExpressions = $ValueExpressions;
    }
    
    public function Traverse(ExpressionWalker $Walker) {
        return $Walker->WalkValueList($this);
    }
    
    /**
     * @return Expression[]
     */
    public function GetValueExpressions() {
        return $this->ValueExpressions;
    }
    
    /**
     * @return self
     */
    public function Update(array $ValueExpressions) {
        if($this->ValueExpressions === $ValueExpressions) {
            return $this;
        }
        
        return new self($ValueExpressions);
    }
}

?>