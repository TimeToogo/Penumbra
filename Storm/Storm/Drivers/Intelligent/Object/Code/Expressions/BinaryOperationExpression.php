<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class BinaryOperationExpression extends ValueExpression {
    private $LeftOperandExpression;
    private $Operator;
    private $RightOperandExpression;
    public function __construct(ValueExpression $LeftOperandExpression, $Operator, ValueExpression $RightOperandExpression) {
        $this->LeftOperandExpression = $LeftOperandExpression;
        $this->Operator = $Operator;
        $this->RightOperandExpression = $RightOperandExpression;
    }
    
    public function GetOperator() {
        return $this->Operator;
    }
        
    /**
     * @return ValueExpression
     */
    public function GetLeftOperandExpression() {
        return $this->LeftOperandExpression;
    }
        
    /**
     * @return ValueExpression
     */
    public function GetRightOperandExpression() {
        return $this->RightOperandExpression;
    }
    
    protected function CompileCode(&$Code) {
        $Code .= '(' . $this->LeftOperandExpression->Compile() . ')' . 
                $this->Operator . 
                '(' . $this->RightOperandExpression->Compile() . ')';
    }
}

?>