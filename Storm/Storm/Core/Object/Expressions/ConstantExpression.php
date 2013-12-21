<?php

namespace Storm\Core\Object\Expressions;

class ConstantExpression extends Expression {
    private $Value;
    public function __construct($Value) {
        $this->Value = $Value;
    }
    
    public function GetValue() {
        return $this->Value;
    }
}

?>