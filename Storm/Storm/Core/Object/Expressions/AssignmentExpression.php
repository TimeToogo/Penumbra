<?php

namespace Storm\Core\Object\Expressions;

use \Storm\Core\Object\IProperty;

/**
 * Expression representing an assignment to an entity property.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class AssignmentExpression extends Expression {
    private $PropertyExpression;
    private $Operator;
    private $AssignmentValueExpression;
    public function __construct(PropertyExpression $PropertyExpression, $Operator, Expression $AssignmentValueExpression) {
        $this->PropertyExpression = $PropertyExpression;
        $this->Operator = $Operator;
        $this->AssignmentValueExpression = $AssignmentValueExpression;
    }
    
    /**
     * @return string The assignment operator
     */
    public function GetOperator() {
        return $this->Operator;
    }
        
    /**
     * @return PropertyExpression
     */
    public function GetPropertyExpression() {
        return $this->PropertyExpression;
    }
        
    /**
     * @return Expression
     */
    public function GetAssignmentValueExpression() {
        return $this->AssignmentValueExpression;
    }
    
    /**
     * @return self
     */
    public function Update(PropertyExpression $PropertyExpression, $Operator, Expression $AssignmentValueExpression) {
        if($this->PropertyExpression === $PropertyExpression
                && $this->Operator === $Operator
                && $this->AssignmentValueExpression === $AssignmentValueExpression) {
            return $this;
        }
        
        return new self($PropertyExpression, $Operator, $AssignmentValueExpression);
    }
}

?>