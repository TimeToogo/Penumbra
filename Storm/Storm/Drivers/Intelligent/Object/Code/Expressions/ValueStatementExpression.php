<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class ValueStatementExpression extends StatementExpression {
    private $ValueExpression;
    public function __construct(ValueExpression $ValueExpression) {
        $this->ValueExpression = $ValueExpression;
    }
    
    protected function CompileStatement(&$Code) {
        $Code .= $this->ValueExpression->Compile();
    }
}

?>