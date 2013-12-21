<?php

namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Core\Relational\Expressions\Expression as CoreExpression;

class UnaryOperationExpression extends Expression {
    private $Operator;
    private $OperandExpression;
    public function __construct($UnaryOperator, CoreExpression $OperandExpression) {
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