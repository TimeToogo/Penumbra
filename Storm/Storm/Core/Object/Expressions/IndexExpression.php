<?php

namespace Storm\Core\Object\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class IndexExpression extends ObjectOperationExpression {
    private $IndexExpression;
    
    public function __construct(Expression $ObjectOrNewExpression, Expression $IndexExpression) {
        parent::__construct($ObjectOrNewExpression);
        
        $this->IndexExpression = $IndexExpression;
    }
    
    /**
     * @return Expression
     */
    public function GetIndexExpression() {
        return $this->IndexExpression;
    }
}

?>