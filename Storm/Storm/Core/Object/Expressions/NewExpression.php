<?php

namespace Storm\Core\Object\Expressions;

class NewExpression extends MethodCallExpression {
    public function __construct($ClassType, array $ArgumentValueExpressions = array()) {
        parent::__construct(new ObjectExpression($ClassType), '__construct', $ArgumentValueExpressions);
    }
}

?>