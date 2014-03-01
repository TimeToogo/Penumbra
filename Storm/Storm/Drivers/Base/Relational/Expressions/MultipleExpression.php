<?php

 namespace Storm\Drivers\Base\Relational\Expressions;

class MultipleExpression extends Expression {
    private $Expressions;
    
    public function __construct(array $Expressions) {
        $this->Expressions = $Expressions;
    }
    
    /**
     * @return CoreExpression[]
     */
    public function GetExpressions() {
        return $this->Expressions;
    }
    
    /**
     * @return self
     */
    public function Update(array $Expressions) {
        if($this->Expressions === $Expressions) {
            return $this;
        }
        
        return new self($Expressions);
    }
}

?>