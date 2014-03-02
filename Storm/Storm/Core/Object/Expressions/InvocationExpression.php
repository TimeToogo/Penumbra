<?php

namespace Storm\Core\Object\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class InvocationExpression extends TraversalExpression {
    private $ArgumentExpressions;
    
    public function __construct(Expression $ValueExpression, array $ArgumentExpressions) {
        parent::__construct($ValueExpression);
        
        $this->ArgumentExpressions = $ArgumentExpressions;
    }
    
    /**
     * @return Expression[]
     */
    public function GetArgumentExpressions() {
        return $this->ArgumentExpressions;
    }
}

?>