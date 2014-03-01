<?php

namespace Storm\Drivers\Base\Relational\Expressions;
 
class LiteralExpression extends Expression {
    private $String;
    public function __construct($String) {
        $this->String = $String;
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