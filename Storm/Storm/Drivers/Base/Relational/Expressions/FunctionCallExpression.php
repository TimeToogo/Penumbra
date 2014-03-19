<?php

 namespace Storm\Drivers\Base\Relational\Expressions;

class FunctionCallExpression extends Expression {
    private $Name;
    private $ArgumentExpressions;
    public function __construct($Name, array $ArgumentExpressions) {
        $this->Name = $Name;
        $this->ArgumentExpressions = $ArgumentExpressions;
    }
    
    public function Traverse(ExpressionWalker $Walker) {
        return $Walker->WalkFunctionCall($this);
    }
    
    public function GetName() {
        return $this->Name;
    }
    
    /**
     * @return Expression[]
     */
    public function GetArgumentExpressions() {
        return $this->ArgumentExpressions;
    }
    
    /**
     * @return self
     */
    public function Update($Name, array $ArgumentExpressions) {
        if($this->Name === $Name && $this->ArgumentExpressions === $ArgumentExpressions) {
            return $this;
        }
        
        return new self($Name, $ArgumentExpressions);
    }
}

?>