<?php

namespace Storm\Core\Object\Expressions;

/**
 * Expression representing a cast operation.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class CastExpression extends Expression {
    private $CastType;
    private $CastValueExpression;
    
    public function __construct($CastType, Expression $CastValueExpression) {
        $this->CastType = $CastType;
        $this->CastValueExpression = $CastValueExpression;
    }
    
    /**
     * @return string The cast operator
     */
    public function GetCastType() {
        return $this->CastType;
    }
    
    /**
     * @return Expression The expression which is cast
     */
    public function GetCastValueExpression() {
        return $this->CastValueExpression;
    }
}

?>