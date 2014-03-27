<?php

namespace Penumbra\Core\Object\Expressions;

use \Penumbra\Core\Object\IProperty;

/**
 * Expression representing an assignment to an entity property.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class AssignmentExpression extends Expression {
    private $AssignToExpression;
    private $Operator;
    private $AssignmentValueExpression;
    public function __construct(Expression $AssignToExpression, $Operator, Expression $AssignmentValueExpression) {
        $this->AssignToExpression = $AssignToExpression;
        $this->Operator = $Operator;
        $this->AssignmentValueExpression = $AssignmentValueExpression;
    }
        
    /**
     * @return Expression
     */
    public function GetAssignToExpression() {
        return $this->AssignToExpression;
    }
    
    /**
     * @return string The assignment operator
     */
    public function GetOperator() {
        return $this->Operator;
    }
        
    /**
     * @return Expression
     */
    public function GetAssignmentValueExpression() {
        return $this->AssignmentValueExpression;
    }
    
    public function Traverse(ExpressionWalker $Walker) {
        return $Walker->WalkAssignment($this);
    }
    
    public function Simplify() {
        return $this->Update(
                $this->AssignToExpression->Simplify(),
                $this->Operator,
                $this->AssignmentValueExpression->Simplify());
    }
    
    /**
     * @return self
     */
    public function Update(Expression $AssignToExpression, $Operator, Expression $AssignmentValueExpression) {
        if($this->AssignToExpression === $AssignToExpression
                && $this->Operator === $Operator
                && $this->AssignmentValueExpression === $AssignmentValueExpression) {
            return $this;
        }
        
        return new self($AssignToExpression, $Operator, $AssignmentValueExpression);
    }
}

?>