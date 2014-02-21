<?php

namespace Storm\Core\Object\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class FunctionCallExpression extends Expression {
    private $Name;
    private $ArgumentValueExpressions;
    public function __construct($Name, array $ArgumentValueExpressions = []) {
        $this->Name = $Name;
        $this->ArgumentValueExpressions = $ArgumentValueExpressions;
    }
    
    /**
     * @return string
     */
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