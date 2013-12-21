<?php

namespace Storm\Core\Object\Expressions;

use \Storm\Core\Object\IProperty;

class AssignmentExpression extends BinaryOperationExpression {
    public function __construct(PropertyExpression $AssignToPropertyExpression, $AssignmentOperator, Expression $AssignmentValueExpression) {
        parent::__construct($AssignToPropertyExpression, $AssignmentOperator, $AssignmentValueExpression);
    }
    
    /**
     * @return IProperty
     */
    public function GetProperty() {
        return $this->GetLeftOperandExpression()->GetProperty();
    }
}

?>