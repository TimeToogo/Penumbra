<?php

namespace Storm\Core\Object\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class InvocationExpression extends ObjectOperationExpression {
    private $ArgumentExpressions;
    
    public function __construct(Expression $ObjectExpression, array $ArgumentExpressions) {
        parent::__construct($ObjectExpression);
        
        $this->ArgumentExpressions = $ArgumentExpressions;
    }
    
    /**
     * @return Expression[]
     */
    public function GetArguments() {
        return $this->ArgumentExpressions;
    }
}

?>