<?php

namespace Storm\Core\Object\Expressions;

class MethodCallExpression extends FunctionCallExpression {
    private $ObjectExpression;
    public function __construct(ObjectExpression $ObjectExpression, $Name, array $ArgumentValueExpressions = array()) {
        parent::__construct($Name, $ArgumentValueExpressions);
        $this->ObjectExpression = $ObjectExpression;
    }
    
    public function IsStatic() {
        return $this->ObjectExpression->HasInstance();
    }
    
    /**
     * @return ObjectExpression
     */
    public function GetObjectExpression() {
        return $this->ObjectExpression;
    }
}

?>