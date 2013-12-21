<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class MethodCallExpression extends ValueExpression {
    private $ObjectValueExpression;
    private $NameValueExpression;
    private $ArgumentValueExpressions;
    
    public function __construct(ValueExpression $ObjectValueExpression, ValueExpression $NameValueExpression, 
            array $ArgumentValueExpressions = array()) {
        $this->ObjectValueExpression = $ObjectValueExpression;
        $this->NameValueExpression = $NameValueExpression;
        $this->ArgumentValueExpressions = $ArgumentValueExpressions;
    }
    
    /**
     * @return ValueExpression
     */
    final public function GetObjectValueExpression() {
        return $this->ObjectValueExpression;
    }
    
    /**
     * @return ValueExpression
     */
    final public function GetNameValueExpression() {
        return $this->NameValueExpression;
    }
    
    /**
     * @return ValueExpression[]
     */
    final public function GetArgumentValueExpressions() {
        return $this->ArgumentValueExpressions;
    }
    
    protected function CompileCode(&$Code) {
        $Code .= $this->ObjectValueExpression->Compile() . '->';
        $Code .= '{' . $this->NameValueExpression->Compile() . '}';
        $Code .= '(' . implode(', ', $this->ArgumentValueExpressions) . ')';
    }
}

?>