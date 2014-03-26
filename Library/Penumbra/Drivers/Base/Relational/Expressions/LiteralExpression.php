<?php

namespace Penumbra\Drivers\Base\Relational\Expressions;
 
class LiteralExpression extends Expression {
    private $String;
    public function __construct($String) {
        $this->String = $String;
    }
    
    public function Traverse(ExpressionWalker $Walker) {
        return $Walker->WalkLiteral($this);
    }
    
    public function GetString() {
        return $this->String;
    }
    
    /**
     * @return self
     */
    public function Update($String) {
        if($this->String === $String) {
            return $this;
        }
        
        return new self($String);
    }
}

?>