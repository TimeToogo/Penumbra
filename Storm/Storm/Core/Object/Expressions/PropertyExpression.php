<?php

namespace Storm\Core\Object\Expressions;

use \Storm\Core\Object\IProperty;

/**
 * Expression an entity property.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class PropertyExpression extends Expression {
    private $Property;
    private $ParentPropertyExpression;
    
    public function __construct(IProperty $Property, PropertyExpression $ParentPropertyExpression = null) {
        $this->Property = $Property;
        $this->ParentPropertyExpression = $ParentPropertyExpression;
    }
    
    /**
     * @return IProperty
     */
    public function GetProperty() {
        return $this->Property;
    }
    
    /**
     * @return boolean
     */
    public function HasParentPropertyExpression() {
        return $this->ParentPropertyExpression !== null;
    }
    
    /**
     * @return PropertyExpression|null
     */
    public function GetParentPropertyExpression() {
        return $this->ParentPropertyExpression;
    }
    
    public function Traverse(ExpressionWalker $Walker) {
        return $Walker->WalkProperty($this);
    }
    
    public function Simplify() {
        return $this->Update(
                $this->Property, 
                $this->ParentPropertyExpression !== null ? $this->ParentPropertyExpression->Simplify() : $this->ParentPropertyExpression);
    }
    
    /**
     * @return self
     */
    public function Update(IProperty $Property, PropertyExpression $ParentPropertyExpression = null) {
        if($this->Property === $Property
                && $this->ParentPropertyExpression === $ParentPropertyExpression) {
            return $this;
        }
        
        return new self($Property, $ParentPropertyExpression);
    }
}

?>