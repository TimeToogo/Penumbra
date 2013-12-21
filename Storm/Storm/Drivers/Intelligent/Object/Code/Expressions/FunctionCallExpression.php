<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class FunctionCallExpression extends StatementExpression {
    private $NameValueExpression;
    private $ArgumentValueExpressions;
    public function __construct(ValueExpression $NameValueExpression, array $ArgumentValueExpressions = array()) {
        $this->NameValueExpression = $NameValueExpression;
        $this->ArgumentValueExpressions = $ArgumentValueExpressions;
    }
    
    /**
     * @return ValueExpression
     */
    public function GetNameValueExpression() {
        return $this->NameValueExpression;
    }
    
    /**
     * @return ValueExpression[]
     */
    public function GetArgumentValueExpressions() {
        return $this->ArgumentValueExpressions;
    }

    protected function CompileStatement(&$Code) {
        $Code .= $this->NameValueExpression->Compile();
        $Code .= '(' . implode(', ', $this->ArgumentValueExpressions) . ')';
    }
}

?>