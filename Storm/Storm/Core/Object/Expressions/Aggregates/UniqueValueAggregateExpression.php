<?php

namespace Storm\Core\Object\Expressions\Aggregates;

use \Storm\Core\Object\Expressions\Expression;

/**
 * Expression for an aggregate function.
 * Count, Maximum, Minimum, Average, Sum, Implode, All, Any
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class UniqueValueAggregateExpression extends UniqueAggregateExpression {
    private $ValueExpression;
    
    final public function __construct($UniqueValuesOnly, Expression $ValueExpression) {
        parent::__construct($UniqueValuesOnly);
        $this->ValueExpression = $ValueExpression;
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