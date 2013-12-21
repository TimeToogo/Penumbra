<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class VariableVariableExpression extends ValueExpression {
    private $VariableNameValueExpression;
    public function __construct(ValueExpression $VariableNameValueExpression) {
        $this->VariableNameValueExpression = $VariableNameValueExpression;
    }
    
    /**
     * @return ValueExpression
     */
    public function GetVariableNameValueExpression() {
        return $this->VariableNameValueExpression;
    }
    
    protected function CompileCode(&$Code) {
        $Code .= '${' . $this->VariableNameValueExpression->Compile() . '}';
    }
}

?>