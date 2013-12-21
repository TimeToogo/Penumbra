<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class UnaryOperationExpression extends ValueExpression {
    private $Operator;
    private $OperandExpression;
    public function __construct($UnaryOperator, ValueExpression $OperandExpression) {
        $this->Operator = $UnaryOperator;
        $this->OperandExpression = $OperandExpression;
    }
    
    public function GetOperator() {
        return $this->Operator;
    }
        
    /**
     * @return ValueExpression
     */
    public function GetOperandExpression() {
        return $this->OperandExpression;
    }
    
    protected function CompileCode(&$Code) {
        $Code .= sprintf($this->Operator, '(' . $this->OperandExpression->Compile() . ')');
    }
}

?>