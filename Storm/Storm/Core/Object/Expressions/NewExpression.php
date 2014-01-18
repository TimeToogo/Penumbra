<?php

namespace Storm\Core\Object\Expressions;

/**
 * Expression representing the instantiating of a class.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class NewExpression extends MethodCallExpression {
    public function __construct($ClassType, array $ArgumentValueExpressions = array()) {
        parent::__construct(new ObjectExpression($ClassType), '__construct', $ArgumentValueExpressions);
    }
}

?>