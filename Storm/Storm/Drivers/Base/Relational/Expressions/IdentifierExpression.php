<?php

 namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Core\Relational\Expression as CoreExpression;

class IdentifierExpression extends Expression {
    private $IdentifierSegments;
    
    public function __construct(array $IdentifierSegments) {
        $this->IdentifierSegments = $IdentifierSegments;
    }
    
    public function GetIdentifierSegments() {
        return $this->IdentifierSegments;
    }
    
    /**
     * @return self
     */
    public function Update(array $IdentifierSegments) {
        if($this->IdentifierSegments === $IdentifierSegments) {
            return $this;
        }
        
        return new self($IdentifierSegments);
    }
}

?>