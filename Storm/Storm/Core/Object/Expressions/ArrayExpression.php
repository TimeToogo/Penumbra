<?php

namespace Storm\Core\Object\Expressions;

/**
 * Expression representing an array declaration.
 * NOTE: Does not have support for array keys.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ArrayExpression extends Expression {
    private $KeyExpressions;
    private $ValueExpressions;
    public function __construct(array $KeyExpressions, array $ValueExpressions) {
        if(array_keys($KeyExpressions) !== array_keys($ValueExpressions)) {
            throw new \Storm\Core\Object\ObjectException(
                    'The supplied key expression array keys must match the keys of the value expression array: (%s) !== (%s)',
                    implode(', ', array_keys($KeyExpressions)),
                    implode(', ', array_keys($ValueExpressions)));
        }
        $this->KeyExpressions = $KeyExpressions;
        $this->ValueExpressions = $ValueExpressions;
    }
    
    /**
     * @return Expression[]
     */
    public function GetKeyExpressions() {
        return $this->KeyExpressions;
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
    public function Update(array $KeyExpressions, array $ValueExpressions) {
        if($this->ValueExpressions === $ValueExpressions
                && $this->ValueExpressions === $KeyExpressions) {
            return $this;
        }
        
        return new self($KeyExpressions, $ValueExpressions);
    }
}

?>