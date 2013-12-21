<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class AssignmentExpression extends BinaryOperationExpression {
    public function __construct(ValueExpression $AssignToValueExpression, $AssignmentOperator, ValueExpression $AssignmentValueExpression) {
        parent::__construct($AssignToValueExpression, $AssignmentOperator, $AssignmentValueExpression);
    }
}

?>