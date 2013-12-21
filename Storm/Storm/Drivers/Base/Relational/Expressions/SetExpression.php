<?php

 namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Core\Relational\Expressions\ColumnExpression;
use \Storm\Core\Relational\Expressions\Expression as CoreExpression;
 
class SetExpression extends BinaryOperationExpression {
    public function __construct(
            ColumnExpression $AssignToColumnExpression, 
            $AssignmentOperator, 
            CoreExpression $AssignmentValueExpression) {
        parent::__construct($AssignToColumnExpression, $AssignmentOperator, $AssignmentValueExpression);
    }
}

?>