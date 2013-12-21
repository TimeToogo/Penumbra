<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

abstract class ValuedExpression extends StatementExpression {
    private $HasValueExpression;
    private $ValueExpression;
    public function __construct(ValueExpression $ValueExpression = null) {
        $this->HasValueExpression = $ValueExpression !== null;
        $this->ValueExpression = $ValueExpression;
    }
    
    final public function HasValueExpression() {
        return $this->HasValueExpression;
    }
        
    /**
     * @return ValueExpression
     */
    final public function GetValueExpression() {
        return $this->ValueExpression;
    }
}

?>