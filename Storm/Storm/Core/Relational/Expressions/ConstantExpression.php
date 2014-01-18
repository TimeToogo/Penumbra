<?php

namespace Storm\Core\Relational\Expressions;

/**
 * Expression representing a constant value.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ConstantExpression extends Expression {
    /**
     * @var mixed
     */
    private $Value;
    
    public function __construct($Value) {
        $this->Value = $Value;
    }
    
    /**
     * @return mixed
     */
    public function GetValue() {
        return $this->Value;
    }
}

?>