<?php

namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Core\Relational\Expression as CoreExpression;

class BinaryOperationExpression extends Expression {
    private $LeftOperandExpression;
    private $Operator;
    private $RightOperandExpression;
    public function __construct(
            CoreExpression $LeftOperandExpression, 
            $Operator, 
            CoreExpression $RightOperandExpression) {
        $this->LeftOperandExpression = $LeftOperandExpression;
        $this->Operator = $Operator;
        $this->RightOperandExpression = $RightOperandExpression;
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
            CoreExpression $LeftOperandExpression, 
            $Operator, 
            CoreExpression $RightOperandExpression) {
        if($this->LeftOperandExpression === $LeftOperandExpression
                && $this->Operator === $Operator
                && $this->RightOperandExpression === $RightOperandExpression) {
            return $this;
        }
        
        return new self($LeftOperandExpression, $Operator, $RightOperandExpression);
    }
}

?>