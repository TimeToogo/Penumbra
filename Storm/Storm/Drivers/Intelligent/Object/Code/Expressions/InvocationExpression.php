<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class InvocationExpression extends ValueExpression {
    private $VariableExpression;
    private $ArgumentValueExpressions;
    
    public function __construct(VariableExpression $VariableExpression, array $ArgumentValueExpressions = array()) {
        $this->VariableExpression = $VariableExpression;
        $this->ArgumentValueExpressions = $ArgumentValueExpressions;
    }
    
    /**
     * @return VariableExpression
     */
    public function GetVariableExpression() {
        return $this->VariableExpression;
    }
    
    /**
     * @return ValueExpression[]
     */
    public function GetArgumentValueExpressions() {
        return $this->ArgumentValueExpressions;
    }

    protected function CompileCode(&$Code) {
        $Code .= $this->VariableExpression->Compile();
        $Code .= '(' . implode(', ', $this->ArgumentValueExpressions) . ')';
    }
}

?>