<?php

namespace Storm\Core\Object\Expressions;

/**
 * Represents acting on a value (properties, methods, indexer...)
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class TraversalExpression extends Expression {    
    /**
     * @var Expression
     */
    protected $ValueExpression;
    
    public function __construct(Expression $ValueExpression) {
        
        $this->ValueExpression = $ValueExpression;
    }
    
    /**
     * @return boolean
     */
    final public function OriginatesFrom($ExpressionType) {
        $TraversalExpression = $this;
        while ($TraversalExpression instanceof self) {
            $TraversalExpression = $TraversalExpression->GetValueExpression();
        }
        
        return $TraversalExpression instanceof $ExpressionType;
    }
    
    /**
     * @return Expression
     */
    final public function GetValueExpression() {
        return $this->ValueExpression;
    }
        
    /**
     * @return Expression
     */
    final public function UpdateValue(Expression $ValueExpression) {
        if($this->ValueExpression === $ValueExpression) {
            return $this;
        }
        
        return $this->UpdateValueExpression($ValueExpression);
    }
    protected abstract function UpdateValueExpression(Expression $ValueExpression);
}

?>