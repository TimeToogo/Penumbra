<?php

 namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Core\Relational\Expressions\ColumnExpression;
use \Storm\Core\Relational\Expression as CoreExpression;
 
class SetExpression extends Expression {
    private $AssignToColumnExpression;
    private $Operator;
    private $AssignmentValueExpression;
    
    public function __construct(
            ColumnExpression $AssignToColumnExpression, 
            $Operator, 
            CoreExpression $AssignmentValueExpression) {
        $this->AssignToColumnExpression = $AssignToColumnExpression;
        $this->Operator = $Operator;
        $this->AssignmentValueExpression = $AssignmentValueExpression;
    }
    
    public function GetOperator() {
        return $this->Operator;
    }
        
    /**
     * @return ColumnExpression
     */
    public function GetAssignToColumnExpression() {
        return $this->AssignToColumnExpression;
    }
        
    /**
     * @return CoreExpression
     */
    public function GetAssignmentValueExpression() {
        return $this->AssignmentValueExpression;
    }
    
    /**
     * @return self
     */
    public function Update(
            ColumnExpression $AssignToColumnExpression, 
            $Operator, 
            CoreExpression $AssignmentValueExpression) {
        if($this->AssignToColumnExpression === $AssignToColumnExpression
                && $this->Operator === $Operator
                && $this->AssignmentValueExpression === $AssignmentValueExpression) {
            return $this;
        }
        
        return new self($AssignToColumnExpression, $Operator, $AssignmentValueExpression);
    }
}

?>