<?php

 namespace Penumbra\Drivers\Base\Relational\Expressions;

class MultipleExpression extends Expression {
    private $Expressions;
    
    public function __construct(array $Expressions) {
        $this->Expressions = $Expressions;
    }
    
    public function Traverse(ExpressionWalker $Walker) {
        return $Walker->WalkMultiple($this);
    }
    
    /**
     * @return Expression[]
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