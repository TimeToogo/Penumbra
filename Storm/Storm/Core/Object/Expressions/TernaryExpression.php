<?php

namespace Storm\Core\Object\Expressions;

class TernaryExpression extends Expression {
    private $ConditionExpression;
    private $IfTrueExpression;
    private $IfFalseExpression;
    
    public function __construct(
            Expression $ConditionExpression,
            Expression $IfTrueExpression, 
            Expression $IfFalseExpression) {
        $this->ConditionExpression = $ConditionExpression;
        $this->IfTrueExpression = $IfTrueExpression;
        $this->IfFalseExpression = $IfFalseExpression;
    }
    
    public function GetConditionExpression() {
        return $this->ConditionExpression;
    }

    public function GetIfTrueExpression() {
        return $this->IfTrueExpression;
    }

    public function GetIfFalseExpression() {
        return $this->IfFalseExpression;
    }
}

?>