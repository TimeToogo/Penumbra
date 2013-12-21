<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class CastExpression extends ValueExpression {
    private $CastType;
    private $CastValueExpression;
    
    public function __construct($CastType, ValueExpression $CastValueExpression) {
        $this->CastType = $CastType;
        $this->CastValueExpression = $CastValueExpression;
    }
    
    public function GetCastType() {
        return $this->CastType;
    }
    
    /**
     * @return ValueExpression
     */
    public function GetCastValueExpression() {
        return $this->CastValueExpression;
    }

    protected function CompileCode(&$Code) {
        $Code .= '(' . $this->CastType . ')(' . $this->CastValueExpression->Compile() . ')';
    }
}

?>