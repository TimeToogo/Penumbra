<?php

namespace Storm\Core\Object\Expressions;

/**
 * Expression representing the instantiating of a class.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class NewExpression extends Expression {
    private $ClassType;
    private $ArgumentValueExpressions;
    
    public function __construct($ClassType, array $ArgumentValueExpressions = []) {
        $this->ClassType = $ClassType;
        $this->ArgumentValueExpressions = $ArgumentValueExpressions;
    }
    
    public function GetClassType() {
        return $this->ClassType;
    }
    
    /**
     * @return Expression[]
     */
    public function GetArgumentValueExpressions() {
        return $this->ArgumentValueExpressions;
    }
}

?>