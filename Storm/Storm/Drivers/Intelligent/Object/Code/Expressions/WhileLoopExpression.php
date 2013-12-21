<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class WhileLoopExpression extends BodiedExpression {
    private $ConditionalExpression;
    
    public function __construct(ValueExpression $ConditionalExpression, BlockExpression $BodyExpression) {
        parent::__construct($BodyExpression);
        $this->ConditionalExpression = $ConditionalExpression;
    }
    
    /**
     * @return ValueExpression
     */
    public function GetConditionalExpression() {
        return $this->ConditionalExpression;
    }

    protected function CompileCode(&$Code) {
        $Code .= 'while';
        $Code .= '(' . $this->ConditionalExpression->Compile() . ')';
        $Code .= $this->GetBodyExpression()->Compile();
    }

}

?>