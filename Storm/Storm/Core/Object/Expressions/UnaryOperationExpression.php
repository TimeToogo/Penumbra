<?php

namespace Storm\Core\Object\Expressions;

class UnaryOperationExpression extends ValueExpression {
    private $Operator;
    private $OperandExpression;
    public function __construct($UnaryOperator, Expression $OperandExpression) {
        $this->Operator = $UnaryOperator;
        $this->OperandExpression = $OperandExpression;
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
}

?>