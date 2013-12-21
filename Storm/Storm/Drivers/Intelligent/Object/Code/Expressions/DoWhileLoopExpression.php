<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class DoWhileLoopExpression extends WhileLoopExpression {
    public function __construct(ValueExpression $ConditionalExpression, BlockExpression $BodyExpression) {
        parent::__construct($ConditionalExpression, $BodyExpression);
    }
    
    protected function CompileCode(&$Code) {
        $Code .= 'do';
        $Code .= $this->GetBodyExpression()->Compile();
        $Code .= 'while ';
        $Code .= '(' . $this->GetConditionalExpression()->Compile() . ');';
    }
}

?>