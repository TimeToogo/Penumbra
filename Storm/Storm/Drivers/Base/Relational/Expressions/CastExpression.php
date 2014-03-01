<?php

namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Core\Relational\Expression as CoreExpression;

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
     * @return CoreExpression
     */
    public function GetCastValueExpression() {
        return $this->CastValueExpression;
    }
    
    /**
     * @return self
     */
    public function Update($CastType, CoreExpression $CastValueExpression) {
        if($this->CastType === $CastType && $this->CastValueExpression === $CastValueExpression) {
            return $this;
        }
        
        return new self($CastType, $CastValueExpression);
    }
}

?>