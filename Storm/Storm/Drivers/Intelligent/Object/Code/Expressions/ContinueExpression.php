<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class ContinueExpression extends ValuedExpression {
    public function __construct(ValueExpression $ValueExpression = null) {
        parent::__construct($ValueExpression);
    }

    protected function CompileStatement(&$Code) {
        $Code .= 'continue';
        if($this->HasValueExpression())
            $Code .= ' ' . $this->GetValueExpression()->Compile();
    }
}

?>