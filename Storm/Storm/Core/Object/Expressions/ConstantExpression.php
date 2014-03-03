<?php

namespace Storm\Core\Object\Expressions;

/**
 * Expression representing a resolved value.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ConstantExpression extends Expression {
    private $Value;
    public function __construct($Value) {
        $this->Value = $Value;
    }
    
    /**
     * @return mixed The constant value
     */
    public function GetValue() {
        return $this->Value;
    }
    
    /**
     * @return self
     */
    public function Update($Value) {
        if($this->Value === $Value) {
            return $this;
        }
        
        return new self($Value);
    }
}

?>