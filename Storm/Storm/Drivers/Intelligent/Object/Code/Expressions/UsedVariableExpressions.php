<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class UsedVariableExpression extends Expression {
    private $Name;
    private $IsReference;
    
    public function __construct($Name, $IsReference) {
        $this->Name = $Name;
        $this->IsReference = $IsReference;
    }
    
    public function GetName() {
        return $this->Name;
    }

    public function IsReference() {
        return $this->IsReference;
    }

    protected function CompileCode(&$Code) {
        if($this->IsReference)
            $Code .= '&';
        
        $Code .= '$' . $this->Name;
    }
}

?>