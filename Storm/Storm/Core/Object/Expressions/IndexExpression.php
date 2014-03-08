<?php

namespace Storm\Core\Object\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class IndexExpression extends TraversalExpression {
    private $Index;
    
    public function __construct(Expression $ValueExpression, $Index) {
        parent::__construct($ValueExpression);
        
        $this->Index = $Index;
    }
    
    /**
     * @return mixed
     */
    public function GetIndex() {
        return $this->Index;
    }
    
    public function Traverse(ExpressionWalker $Walker) {
        return $Walker->WalkIndex($this);
    }
    
    public function Simplify() {
        $ValueExpression = $this->ValueExpression->Simplify();
        
        if($ValueExpression instanceof ValueExpression) {
            $Value = $ValueExpression;
            return Expression::Value($Value[$this->Index]);
        }        
        
        return $this->Update(
                $ValueExpression,
                $this->Index);
    }
    
    /**
     * @return self
     */
    public function Update(Expression $ValueExpression, $Index) {
        if($this->ValueExpression === $ValueExpression
                && $this->Index === $Index) {
            return $this;
        }
        
        return new self($ValueExpression, $Index);
    }
    
    protected function UpdateValueExpression(Expression $ValueExpression) {
        return new self($ValueExpression, $this->Index);
    }
}

?>