<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

abstract class BodiedExpression extends Expression {
    private $BodyExpression;
    public function __construct(Expression $BodyExpression) {
        $this->BodyExpression = $BodyExpression;
    }
    
    /**
     * @return Expression
     */
    final public function GetBodyExpression() {
        return $this->BodyExpression;
    }
}

?>