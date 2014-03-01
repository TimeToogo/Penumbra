<?php

namespace Storm\Core\Object\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class MemberExpression extends Expression {
    private $ValueExpression;
    
    public function __construct(Expression $ValueExpression) {
        $this->ValueExpression = $ValueExpression;
    }
    
    /**
     * @return Expression
     */
    final public function GetValueExpression() {
        return $this->ValueExpression;
    }
}

?>