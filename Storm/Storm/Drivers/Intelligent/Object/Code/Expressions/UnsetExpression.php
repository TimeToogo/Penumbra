<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class UnsetExpression extends ValueExpression {
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
        $Code .= 'unset(' . implode(', ', $this->ValueExpressions) . ')';
    }

}

?>