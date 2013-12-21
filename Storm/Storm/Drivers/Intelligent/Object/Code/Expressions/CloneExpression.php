<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class CloneExpression extends ValueExpression {
    private $CloneValueExpression;
    
    public function __construct(ValueExpression $CloneValueExpression) {
        $this->CloneValueExpression = $CloneValueExpression;
    }
    
    /**
     * @return ValueExpression
     */
    public function GetCloneValueExpression() {
        return $this->CloneValueExpression;
    }

    protected function CompileCode(&$Code) {
        $Code .=  '(clone ' . $this->CloneValueExpression->Compile() . ')';
    }
}

?>