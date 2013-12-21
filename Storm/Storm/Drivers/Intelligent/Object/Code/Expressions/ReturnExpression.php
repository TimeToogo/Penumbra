<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class ReturnExpression extends ValuedExpression {
    public function __construct(ValueExpression $ValueExpression = null) {
        parent::__construct($ValueExpression);
    }
    
    protected function CompileStatement(&$Code) {
        $Code .= 'return';
        if($this->HasValueExpression())
            $Code .= ' ' . $this->GetValueExpression()->Compile();
    }
}

?>