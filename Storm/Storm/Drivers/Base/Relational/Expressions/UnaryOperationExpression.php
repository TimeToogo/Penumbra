<?php

namespace Storm\Drivers\Base\Relational\Expressions;

class UnaryOperationExpression extends Expression {
    private $Operator;
    private $OperandExpression;
    public function __construct($UnaryOperator, Expression $OperandExpression) {
        $this->Operator = $UnaryOperator;
        $this->OperandExpression = $OperandExpression;
    }
    
    public function Traverse(ExpressionWalker $Walker) {
        return $Walker->WalkUnaryOperation($this);
    }
    
    public function GetOperator() {
        return $this->Operator;
    }
        
    /**
     * @return Expression
     */
    public function GetOperandExpression() {
        return $this->OperandExpression;
    }
    
    /**
     * @return self
     */
    public function Update($UnaryOperator, Expression $OperandExpression) {
        if($this->Operator === $UnaryOperator
                && $this->OperandExpression === $OperandExpression) {
            return $this;
        }
        
        return new self($UnaryOperator, $OperandExpression);
    }
}

?>