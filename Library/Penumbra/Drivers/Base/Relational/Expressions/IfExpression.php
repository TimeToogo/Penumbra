<?php

 namespace Penumbra\Drivers\Base\Relational\Expressions;

use \Penumbra\Core\Relational\Expression as CoreExpression;
 
class IfExpression extends Expression {
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
    
    public function Traverse(ExpressionWalker $Walker) {
        return $Walker->WalkIf($this);
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