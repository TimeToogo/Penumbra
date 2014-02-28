<?php

 namespace Storm\Drivers\Base\Relational\Expressions;

class AggregateExpression extends FunctionCallExpression {
    public function __construct($Name, ValueListExpression $ArgumentValueListExpression) {
        parent::__construct($Name, $ArgumentValueListExpression);
    }
}

?>