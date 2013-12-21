<?php

namespace Storm\Core\Object\Expressions;

class FunctionCallExpression extends Expression {
    private $Name;
    private $ArgumentValueExpressions;
    public function __construct($Name, array $ArgumentValueExpressions = array()) {
        $this->Name = $Name;
        $this->ArgumentValueExpressions = $ArgumentValueExpressions;
    }
    
    public function GetName() {
        return $this->Name;
    }
    
    /**
     * @return Expression[]
     */
    public function GetArgumentValueExpressions() {
        return $this->ArgumentValueExpressions;
    }
}

?>