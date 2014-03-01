<?php

 namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Core\Relational\Expression as CoreExpression;
 
class IfExpression extends Expression {
    private $ConditionExpression;
    private $IfTrueExpression;
    private $IfFalseExpression;
    
    public function __construct(
            CoreExpression $ConditionExpression,
            CoreExpression $IfTrueExpression, 
            CoreExpression $IfFalseExpression) {
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
    
    /**
     * @return self
     */
    public function Update(
            CoreExpression $ConditionExpression,
            CoreExpression $IfTrueExpression, 
            CoreExpression $IfFalseExpression) {
        if($this->ConditionExpression === $ConditionExpression
                && $this->IfTrueExpression = $IfTrueExpression
                && $this->IfFalseExpression = $IfFalseExpression) {
            return $this;
        }
        
        return new self($ConditionExpression, $IfTrueExpression, $IfFalseExpression);
    }
}

?>