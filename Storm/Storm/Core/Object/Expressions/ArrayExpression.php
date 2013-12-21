<?php

namespace Storm\Core\Object\Expressions;

class ArrayExpression extends Expression {
    private $ValueExpressions;
    public function __construct(array $ValueExpressions) {
        $this->ValueExpressions = $ValueExpressions;
    }
    
    /**
     * @return Expression[]
     */
    public function GetValueExpressions() {
        return $this->ValueExpressions;
    }
}

?>