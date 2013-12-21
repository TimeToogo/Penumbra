<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class DefaultExpression extends Expression {
    private $BodyExpression;
    function __construct(Expression $BodyExpression) {
        $this->BodyExpression = $BodyExpression;
    }
    
    /**
     * @return Expression
     */
    public function GetStatementExpression() {
        return $this->BodyExpression;
    }

    protected function CompileCode(&$Code) {
        $Code .= 'default: ';
        $Code .= $this->BodyExpression->Compile();
    }

}

?>