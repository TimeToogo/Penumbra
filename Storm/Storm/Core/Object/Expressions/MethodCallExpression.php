<?php

namespace Storm\Core\Object\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class MethodCallExpression extends ObjectOperationExpression {
    private $Name;
    private $ArgumentValueExpressions;
    
    public function __construct(Expression $ObjectOrNewExpression, $Name, array $ArgumentValueExpressions = []) {
        parent::__construct($ObjectOrNewExpression);
        
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