<?php

namespace Storm\Core\Object\Expressions;

class BinaryOperationExpression extends Expression {
    private $LeftOperandExpression;
    private $Operator;
    private $RightOperandExpression;
    public function __construct(Expression $LeftOperandExpression, $Operator, Expression $RightOperandExpression) {
        $this->LeftOperandExpression = $LeftOperandExpression;
        $this->Operator = $Operator;
        $this->RightOperandExpression = $RightOperandExpression;
    }
    
    public function GetOperator() {
        return $this->Operator;
    }
        
    /**
     * @return Expression
     */
    public function GetLeftOperandExpression() {
        return $this->LeftOperandExpression;
    }
        
    /**
     * @return Expression
     */
    public function GetRightOperandExpression() {
        return $this->RightOperandExpression;
    }
}

?>