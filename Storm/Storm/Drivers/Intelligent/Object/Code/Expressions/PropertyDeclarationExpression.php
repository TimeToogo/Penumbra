<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class PropertyDeclarationExpression extends StatementExpression {
    private $AccessLevel;
    private $IsStatic;
    private $Name;
    private $HasValueExpression;
    private $ValueExpression;
    function __construct($AccessLevel, $IsStatic, $Name, ValueExpression $ValueExpression = null) {
        $this->AccessLevel = $AccessLevel;
        $this->IsStatic = $IsStatic;
        $this->Name = $Name;
        $this->HasValueExpression = $ValueExpression !== null;
        $this->ValueExpression = $ValueExpression;
    }
    
    public function GetAccessLevel() {
        return $this->AccessLevel;
    }

    public function IsStatic() {
        return $this->IsStatic;
    }
  
    public function GetName() {
        return $this->Name;
    }
    
    public function HasValueExpression() {
        return $this->HasValueExpression;
    }

        /**
     * @return ValueExpression
     */
    public function GetValueExpression() {
        return $this->ValueExpression;
    }

    protected function CompileStatement(&$Code) {
        $Code .= $this->AccessLevel . ' ';
        if($this->IsStatic)
            $Code .= 'static ';
        
        $Code .= '$' . $this->Name;
        if($this->HasValueExpression) {
            $Code .= ' = ' . $this->ValueExpression->Compile();
        }
    }

}

?>