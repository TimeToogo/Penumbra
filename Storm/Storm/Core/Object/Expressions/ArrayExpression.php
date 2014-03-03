<?php

namespace Storm\Core\Object\Expressions;

/**
 * Expression representing an array declaration.
 * NOTE: Does not have support for array keys.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
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
    
    /**
     * @return self
     */
    public function Update(array $ValueExpressions) {
        if($this->ValueExpressions === $ValueExpressions) {
            return $this;
        }
        
        return new self($ValueExpressions);
    }
}

?>