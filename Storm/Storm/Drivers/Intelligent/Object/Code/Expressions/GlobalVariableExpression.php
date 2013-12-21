<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class GlobalVariableExpression extends StatementExpression {
    private $VariableExpressions;
    
    public function __construct(array $VariableExpressions) {
        $this->VariableExpressions = $VariableExpressions;
    }
    
    public function GetVariableExpressions() {
        return $this->VariableExpressions;
    }

    protected function CompileStatement(&$Code) {
        $Code .= 'global ';
        
        $Code .= implode(', ', $this->VariableExpressions);
    }
}

?>