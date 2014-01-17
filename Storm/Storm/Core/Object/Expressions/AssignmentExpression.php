<?php

namespace Storm\Core\Object\Expressions;

use \Storm\Core\Object\IProperty;

class AssignmentExpression extends BinaryOperationExpression {
    private $AssignToPropertyExpression;
    
    public function __construct(PropertyExpression $AssignToPropertyExpression, $AssignmentOperator, Expression $AssignmentValueExpression) {
        parent::__construct($AssignToPropertyExpression, $AssignmentOperator, $AssignmentValueExpression);
        
        $this->AssignToPropertyExpression = $AssignToPropertyExpression;
    }
    
    /**
     * @return IProperty
     */
    public function GetProperty() {
        return $this->AssignToPropertyExpression->GetProperty();
    }
}

?>