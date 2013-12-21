<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class TernaryExpression extends ValueExpression {
    private $ConditionExpression;
    private $HasIfTrueExpression;
    private $IfTrueExpression;
    private $ElseExpression;
    public function __construct(ValueExpression $ConditionExpression, ValueExpression $ElseExpression, ValueExpression $IfTrueExpression = null) {
        $this->ConditionExpression = $ConditionExpression;
        $this->HasIfTrueExpression = $IfTrueExpression !== null;
        $this->IfTrueExpression = $IfTrueExpression;
        $this->ElseExpression = $ElseExpression;
    }
    
    public function HasIfTrueExpression() {
        return $this->HasIfTrueExpression;
    }
        
    /**
     * @return ValueExpression
     */
    public function GetConditionExpression() {
        return $this->ConditionExpression;
    }
    
    /**
     * @return Expression
     */
    public function GetIfTrueExpression() {
        return $this->IfTrueExpression;
    }

    /**
     * @return Expression
     */
    public function GetElseExpression() {
        return $this->ElseExpression;
    }

    protected function CompileCode(&$Code) {
        $Code .= '(';
        
        $Code .= '(' . $this->ConditionExpression->Compile() . ') ';
        $Code .= '?';
        
        if($this->HasIfTrueExpression)
            $Code .= ' ' . $this->IfTrueExpression->Compile() . ' ';
        
        $Code .= ': ' . $this->ElseExpression->Compile();
        
        $Code .= ')';
    }

}

?>