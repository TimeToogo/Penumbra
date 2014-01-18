<?php

namespace Storm\Core\Object\Expressions;

/**
 * Expression representing a constant value.
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
}

?>