<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class IssetExpression extends ValueExpression {
    private $ValueExpressions;
    public function __construct(array $ValueExpressions) {
        $this->ValueExpressions = $ValueExpressions;
    }
    
    /**
     * @return ValueExpression[]
     */
    public function GetValueExpressions() {
        return $this->ValueExpressions;
    }
    
    protected function CompileCode(&$Code) {
        $Code .= 'isset(' . implode(', ', $this->ValueExpressions) . ')';
    }
}

?>