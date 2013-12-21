<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class StaticMethodCallExpression extends ValueExpression {
    private $ClassValueExpression;
    private $NameValueExpression;
    private $ArgumentValueExpressions;
    
    public function __construct(ValueExpression $ClassValueExpression, ValueExpression $NameValueExpression, 
            array $ArgumentValueExpressions = array()) {
        $this->ClassValueExpression = $ClassValueExpression;
        $this->NameValueExpression = $NameValueExpression;
        $this->ArgumentValueExpressions = $ArgumentValueExpressions;
    }
    
    /**
     * @return ValueExpression
     */
    final public function GetClassValueExpression() {
        return $this->ClassValueExpression;
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
        $Code .= $this->ClassValueExpression->Compile() . '::';
        $Code .= '{' . $this->NameValueExpression->Compile() . '}';
        $Code .= '(' . implode(', ', $this->ArgumentValueExpressions) . ')';
    }
}

?>