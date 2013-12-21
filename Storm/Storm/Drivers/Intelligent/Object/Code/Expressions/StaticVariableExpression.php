<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class StaticVariableExpression extends StatementExpression {
    private $Name;
    private $HasValueExpression;
    private $ValueExpression;
    
    public function __construct($Name, ValueExpression $ValueExpression = null) {
        $this->Name = $Name;
        $this->HasValueExpression = $ValueExpression !== null;
        $this->ValueExpression = $ValueExpression;
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
        $Code .= 'static $' . $this->Name;
        
        if($this->HasValueExpression) {
            $Code .= ' = ' . $this->ValueExpression->Compile();
        }
    }
}

?>