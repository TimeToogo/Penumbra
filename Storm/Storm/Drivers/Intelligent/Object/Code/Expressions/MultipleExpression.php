<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class MultipleExpression extends Expression {
    private $Expressions;
    public function __construct(array $Expressions) {
        $this->Expressions = $Expressions;
    }
    
    /**
     * @return Expression[]
     */
    public function GetExpressions() {
        return $this->Expressions;
    }

    protected function CompileCode(&$Code) {
        foreach($this->Expressions as $Expression) {
            $Code .= $Expression;
        }
    }
}

?>