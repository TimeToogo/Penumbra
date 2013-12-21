<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

abstract class MultipleValuedExpression extends StatementExpression {
    private $ValueExpressions;
    public function __construct(array $ValueExpressions) {
        $this->ValueExpressions = $ValueExpressions;
    }
        
    /**
     * @return ValueExpression[]
     */
    final public function GetValueExpressions() {
        return $this->ValueExpressions;
    }
}

?>