<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class VariableExpression extends ValueExpression {
    private $Name;
    public function __construct($Name) {
        parent::__construct();
        $this->Name = $Name;
    }
    
    public function GetName() {
        return $this->Name;
    }

    protected function CompileCode(&$Code) {
        $Code .= '${' . var_export($this->Name) . '}';
    }
    
    /**
     * @return InvocationExpression
     */
    final public function Invoke(array $ArgumentValueExpressions = array()) {
        return new InvocationExpression($this, $ArgumentValueExpressions);
    }
}

?>