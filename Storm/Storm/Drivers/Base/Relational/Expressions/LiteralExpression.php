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
}

?>