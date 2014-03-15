<?php

namespace Storm\Drivers\Base\Relational\Expressions;

class BinaryOperationExpression extends Expression {
    private $LeftOperandExpression;
    private $Operator;
    private $RightOperandExpression;
    public function __construct(
            Expression $LeftOperandExpression, 
            $Operator, 
            Expression $RightOperandExpression) {
        $this->LeftOperandExpression = $LeftOperandExpression;
        $this->Operator = $Operator;
        $this->RightOperandExpression = $RightOperandExpression;
    }
    
    public function Traverse(ExpressionWalker $Walker) {
        return $Walker->WalkBinaryOperation($this);
    }
    
    public function GetOperator() {
        return $this->Operator;
    }
        
    /**
     * @return CoreExpression
     */
    public function GetLeftOperandExpression() {
        return $this->LeftOperandExpression;
    }
        
    /**
     * @return CoreExpression
     */
    public function GetRightOperandExpression() {
        return $this->RightOperandExpression;
    }
    
    /**
     * @return self
     */
    public function Update(
            Expression $LeftOperandExpression, 
            $Operator, 
            Expression $RightOperandExpression) {
        if($this->LeftOperandExpression === $LeftOperandExpression
                && $this->Operator === $Operator
                && $this->RightOperandExpression === $RightOperandExpression) {
            return $this;
        }
        
        return new self($LeftOperandExpression, $Operator, $RightOperandExpression);
    }
}

?>