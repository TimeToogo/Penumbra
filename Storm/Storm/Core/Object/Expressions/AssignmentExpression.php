<?php

namespace Storm\Core\Object\Expressions;

use \Storm\Core\Object\IProperty;

/**
 * Expression representing an assignment to an entity property.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class AssignmentExpression extends BinaryOperationExpression {
    private $AssignToPropertyExpression;
    
    public function __construct(PropertyExpression $AssignToPropertyExpression, $AssignmentOperator, Expression $AssignmentValueExpression) {
        parent::__construct($AssignToPropertyExpression, $AssignmentOperator, $AssignmentValueExpression);
        
        $this->AssignToPropertyExpression = $AssignToPropertyExpression;
    }
    
    /**
     * @return IProperty The assigned property
     */
    public function GetProperty() {
        return $this->AssignToPropertyExpression->GetProperty();
    }
}

?>