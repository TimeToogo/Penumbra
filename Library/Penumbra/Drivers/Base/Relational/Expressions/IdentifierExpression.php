<?php

 namespace Penumbra\Drivers\Base\Relational\Expressions;

use \Penumbra\Core\Relational\Expression as CoreExpression;

class IdentifierExpression extends Expression {
    private $IdentifierSegments;
    
    public function __construct(array $IdentifierSegments) {
        $this->IdentifierSegments = $IdentifierSegments;
    }
    
    public function Traverse(ExpressionWalker $Walker) {
        return $Walker->WalkIdentifier($this);
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