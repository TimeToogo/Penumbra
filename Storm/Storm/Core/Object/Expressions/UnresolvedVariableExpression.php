<?php

namespace Storm\Core\Object\Expressions;

/**
 * Expression representing a resolved value.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class UnresolvedVariableExpression extends Expression {
    private $NameExpression;
    public function __construct(Expression $NameExpression) {
        $this->NameExpression = $NameExpression;
    }
    
    public function Traverse(ExpressionWalker $Walker) {
        return $Walker->WalkUnresolvedValue($this);
    }
    
    public function Simplify() {
        return $this->Update($this->NameExpression->Simplify());
    }
    
    /**
     * @return Expression
     */
    public function GetNameExpression() {
        return $this->NameExpression;
    }
    
    public function Update(Expression $NameExpression) {
        if($this->NameExpression === $NameExpression) {
            return $this;
        }
        
        return new self($NameExpression);
    }
}

?>