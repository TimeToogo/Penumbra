<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class ListExpression extends ValueExpression {
    private $VariableExpressions;
    public function __construct(array $VariableExpressions) {
        $this->VariableExpressions = $VariableExpressions;
    }
    
    /**
     * @return VariableExpression[]
     */
    public function GetVariableExpressions() {
        return $this->VariableExpressions;
    }
    
    protected function CompileCode(&$Code) {
        $Code .= 'list';
        $Code .= '(' . implode(', ', $this->VariableExpressions) . ')';
    }
}

?>