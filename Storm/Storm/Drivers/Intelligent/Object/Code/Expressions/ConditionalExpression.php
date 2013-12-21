<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class ConditionalExpression extends Expression {
    private $ConditionExpression;
    private $IfTrueExpression;
    private $HasElseExpression;
    private $ElseExpression;
    public function __construct(ValueExpression $ConditionExpression, Expression $IfTrueExpression, Expression $ElseExpression = null) {
        $this->ConditionExpression = $ConditionExpression;
        $this->IfTrueExpression = $IfTrueExpression;
        $this->HasElseExpression = $ElseExpression !== null;
        $this->ElseExpression = $ElseExpression;
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
    
    public function HasElseExpression() {
        return $this->HasElseExpression;
    }

    /**
     * @return Expression
     */
    public function GetElseExpression() {
        return $this->ElseExpression;
    }

    protected function CompileCode(&$Code) {
        $Code .= 'if (' . $this->ConditionExpression->Compile() . ') ';
        $Code .= $this->IfTrueExpression->Compile();
        if($this->HasElseExpression) {
            $Code .= 'else ' . $this->ElseExpression->Compile();
        }
    }

}

?>