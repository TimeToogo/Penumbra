<?php

namespace Storm\Core\Object\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class BinaryOperationExpression extends Expression {
    private $LeftOperandExpression;
    private $Operator;
    private $RightOperandExpression;
    public function __construct(Expression $LeftOperandExpression, $Operator, Expression $RightOperandExpression) {
        $this->LeftOperandExpression = $LeftOperandExpression;
        $this->Operator = $Operator;
        $this->RightOperandExpression = $RightOperandExpression;
    }
    
    /**
     * @return string The binary operator
     */
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
    
    /**
     * @return self
     */
    public function Update(Expression $LeftOperandExpression, $Operator, Expression $RightOperandExpression) {
        if($this->LeftOperandExpression === $LeftOperandExpression
                && $this->Operator === $Operator
                && $this->RightOperandExpression === $RightOperandExpression) {
            return $this;
        }
        
        return new self($LeftOperandExpression, $Operator, $RightOperandExpression);
    }
}

?>