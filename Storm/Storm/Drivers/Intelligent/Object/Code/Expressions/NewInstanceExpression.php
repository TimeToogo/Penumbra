<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class NewInstanceExpression extends ValueExpression {
    private $ClassTypeValueExpression;
    private $ArgumentValueExpressions;
    
    public function __construct(ValueExpression $ClassTypeValueExpression, array $ArgumentValueExpressions = array()) {
        $this->ClassTypeValueExpression = $ClassTypeValueExpression;
        $this->ArgumentValueExpressions = $ArgumentValueExpressions;
    }
    
    /**
     * @return ValueExpression
     */
    public function GetClassTypeValueExpression() {
        return $this->ClassTypeValueExpression;
    }
        
    /**
     * @return ValueExpression[]
     */
    public function GetArgumentValueExpressions() {
        return $this->ArgumentValueExpressions;
    }

    protected function CompileCode(&$Code) {
        $Code .= '(';
        
        $Code .= 'new ' . $this->ClassTypeValueExpression->Compile();
        $Code .= '(' . implode(', ', $this->ArgumentValueExpressions) . ')';
        
        $Code .= ')';
    }

}

?>