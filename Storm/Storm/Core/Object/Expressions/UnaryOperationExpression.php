<?php

namespace Storm\Core\Object\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class UnaryOperationExpression extends Expression {
    private $Operator;
    private $OperandExpression;
    public function __construct($Operator, Expression $OperandExpression) {
        $this->Operator = $Operator;
        $this->OperandExpression = $OperandExpression;
    }
    
    /**
     * @return string
     */
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
    public function Update($Operator, Expression $OperandExpression) {
        if($this->Operator === $Operator
                && $this->OperandExpression === $OperandExpression) {
            return $this;
        }
        
        return new self($Operator, $OperandExpression);
    }
}

?>