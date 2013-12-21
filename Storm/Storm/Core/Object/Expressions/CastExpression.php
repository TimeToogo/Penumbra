<?php

namespace Storm\Core\Object\Expressions;

class CastExpression extends Expression {
    private $CastType;
    private $CastValueExpression;
    
    public function __construct($CastType, Expression $CastValueExpression) {
        $this->CastType = $CastType;
        $this->CastValueExpression = $CastValueExpression;
    }
    
    public function GetCastType() {
        return $this->CastType;
    }
    
    /**
     * @return Expression
     */
    public function GetCastValueExpression() {
        return $this->CastValueExpression;
    }
}

?>