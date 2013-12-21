<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class CaseExpression extends Expression {
    private $CaseValueExpression;
    private $IfCaseExpression;
    
    public function __construct(ValueExpression $CaseValueExpression, Expression $IfCaseExpression) {
        $this->CaseValueExpression = $CaseValueExpression;
        $this->IfCaseExpression = $IfCaseExpression;
    }
    
    /**
     * @return ValueExpression
     */
    public function GetCaseValueExpression() {
        return $this->CaseValueExpression;
    }
    
    /**
     * @return Expression
     */
    public function GetIfCaseStatementExpression() {
        return $this->IfCaseExpression;
    }

    protected function CompileCode(&$Code) {
        $Code .= 'case ' . $this->CaseValueExpression->Compile() . ': ';
        $Code .= $this->IfCaseExpression->Compile();
    }
}

?>