<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class ConstantDeclarationExpression extends StatementExpression {
    private $Name;
    private $Value;
    public function __construct($Name, $Value) {
        $this->Name = $Name;
        $this->Value = $Value;
    }
    
    public function GetName() {
        return $this->Name;
    }

    public function GetValue() {
        return $this->Value;
    }

    protected function CompileStatement(&$Code) {
        $Code .= 'const ' . $this->Name . ' ' . Operators\Assignment::Equal . ' ' . var_export($this->Value);
    }
}

?>