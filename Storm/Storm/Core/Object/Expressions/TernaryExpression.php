<?php

namespace Storm\Core\Object\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
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
    
    /**
     * @return Expression
     */
    public function GetConditionExpression() {
        return $this->ConditionExpression;
    }

    /**
     * @return Expression
     */
    public function GetIfTrueExpression() {
        return $this->IfTrueExpression;
    }

    /**
     * @return Expression
     */
    public function GetIfFalseExpression() {
        return $this->IfFalseExpression;
    }
    
    /**
     * @return self
     */
    public function Update(
            Expression $ConditionExpression,
            Expression $IfTrueExpression, 
            Expression $IfFalseExpression) {
        if($this->ConditionExpression === $ConditionExpression
                && $this->IfTrueExpression === $IfTrueExpression
                && $this->IfFalseExpression === $IfFalseExpression) {
            return $this;
        }
        
        return new self($ConditionExpression, $IfTrueExpression, $IfFalseExpression);
    }
}

?>