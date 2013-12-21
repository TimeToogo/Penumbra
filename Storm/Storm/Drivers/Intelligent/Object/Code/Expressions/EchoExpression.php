<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class EchoExpression extends MultipleValuedExpression {
    public function __construct(array $ValueExpressions) {
        parent::__construct($ValueExpressions);
    }
    
    protected function CompileStatement(&$Code) {
        $Code .= 'echo ' . implode(', ', $this->GetValueExpressions());
    }
}

?>