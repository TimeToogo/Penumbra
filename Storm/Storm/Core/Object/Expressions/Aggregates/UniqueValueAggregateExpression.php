<?php

namespace Storm\Core\Object\Expressions\Aggregates;

use \Storm\Core\Object\Expressions\Expression;
use \Storm\Core\Object\Expressions\ExpressionWalker;

/**
 * Expression for an aggregate function.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class UniqueValueAggregateExpression extends AggregateExpression {
    private $UniqueValuesOnly;
    private $ValueExpression;
    
    final public function __construct($UniqueValuesOnly, Expression $ValueExpression) {
        $this->UniqueValuesOnly = $UniqueValuesOnly;
        $this->ValueExpression = $ValueExpression;
    }
    
    final public function Traverse(ExpressionWalker $Walker) {
        return $this->Update(
                $this->UniqueValuesOnly,
                $Walker->Walk($this->ValueExpression));
    }
    
    final public function UniqueValuesOnly() {
        return $this->UniqueValuesOnly;
    }
    
    /**
     * @return Expression
     */
    final public function GetValueExpression() {
        return $this->ValueExpression;
    }
    
    final public function Simplify() {
        return $this->Update(
                $this->UniqueValuesOnly, 
                $this->ValueExpression->Simplify());
    }
    
    final public function Update($UniqueValuesOnly, Expression $ValueExpression) {
        if($this->UniqueValuesOnly === $UniqueValuesOnly
                && $this->ValueExpression === $ValueExpression) {
            return $this;
        }
        
        return new static($UniqueValuesOnly, $ValueExpression);
    }
}

?>