<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class ThrowExpression extends ValuedExpression {
    public function __construct(ValueExpression $ExceptionValueExpression) {
        parent::__construct($ExceptionValueExpression);
    }

    protected function CompileStatement(&$Code) {
        $Code .= 'throw';
        if($this->HasValueExpression())
            $Code .= ' ' . $this->GetValueExpression()->Compile();
    }

}

?>