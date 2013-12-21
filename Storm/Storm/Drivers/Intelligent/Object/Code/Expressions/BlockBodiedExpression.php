<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

abstract class BlockBodiedExpression extends Expression {
    private $BodyExpression;
    public function __construct(BlockExpression $BodyExpression) {
        $this->BodyExpression = $BodyExpression;
    }
    
    /**
     * @return BlockExpression
     */
    final public function GetBodyExpression() {
        return $this->BodyExpression;
    }
}

?>