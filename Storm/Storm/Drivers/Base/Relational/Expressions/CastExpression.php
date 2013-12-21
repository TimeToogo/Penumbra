<?php

 namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Core\Relational\Expressions\Expression as CoreExpression;

class CastExpression extends Expression {
    private $CastType;
    private $CastValueExpression;
    
    public function __construct($CastType, CoreExpression $CastValueExpression) {
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