<?php

namespace Penumbra\Drivers\Base\Relational\Expressions;

/**
 * Expression representing a value.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class ValueExpression extends Expression {
    /**
     * @var mixed
     */
    private $Value;
    
    final public function __construct($Value) {
        $this->Value = $Value;
    }
    
    /**
     * @return mixed
     */
    final public function GetValue() {
        return $this->Value;
    }
    
    final public function Update($Value) {
        if($this->Value === $Value) {
            return $this;
        }
        
        return new static($Value);
    }
}

?>