<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class ConstantExpression extends ValueExpression {
    private $Value;
    public function __construct($Value) {
        $this->Value = $Value;
    }
    
    public function GetValue() {
        return $this->Value;
    }

    protected function CompileCode(&$Code) {
        $Code .= var_export($this->Value);
    }

}

?>