<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class ParameterExpression extends VariableExpression {
    private $HasTypeHint;
    private $TypeHint;
    private $IsPassedByReference;
    private $HasDefaultValue;
    private $DefaultValueExpression;
    
    public function __construct($Name, $TypeHint = null, $IsPassedByReference = false, 
            ValueExpression $DefaultValueExpression = null) {
        parent::__construct($Name);
        
        $this->HasTypeHint = $TypeHint !== null;
        $this->TypeHint = $TypeHint;
        $this->IsPassedByReference = $IsPassedByReference;
        $this->HasDefaultValue = $DefaultValueExpression !== null;
        $this->DefaultValueExpression = $DefaultValueExpression;
    }
    
    public function HasTypeHint() {
        return $this->HasTypeHint;
    }
    
    public function GetTypeHint() {
        return $this->TypeHint;
    }
    
    public function IsPassedByReference() {
        return $this->IsPassedByReference;
    }

    public function HasDefaultValue() {
        return $this->HasDefaultValue;
    }
    
    /**
     * @return ValueExpression
     */
    public function GetDefaultValueExpression() {
        return $this->DefaultValueExpression;
    }
    
    protected function CompileCode(&$Code) {
        if($this->HasTypeHint)
            $Code .= $this->TypeHint . ' ';
        
        if($this->IsPassedByReference)
            $Code .= '&';
        
        parent::CompileCode($Code);
        
        if($this->HasDefaultValue) {
            $Code .= ' = ';
            $Code .= $this->DefaultValueExpression->Compile();
        }
    }
}

?>