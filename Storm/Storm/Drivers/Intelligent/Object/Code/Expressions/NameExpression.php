<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class NameExpression extends ValueExpression {
    private $Name;
    public function __construct($Name) {
        $this->Name = $Name;
    }
    
    public function GetName() {
        return $this->Name;
    }

    protected function CompileCode(&$Code) {
        $Code .= $this->Name;
    }
}

?>