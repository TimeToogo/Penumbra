<?php

namespace Storm\Core\Object\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class UnaryOperationExpression extends Expression {
    private $Operator;
    private $OperandExpression;
    public function __construct($UnaryOperator, Expression $OperandExpression) {
        $this->Operator = $UnaryOperator;
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
}

?>