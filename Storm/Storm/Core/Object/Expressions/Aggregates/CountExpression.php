<?php

namespace Storm\Core\Object\Expressions\Aggregates;

use \Storm\Core\Object\Expressions\Expression;
use \Storm\Core\Object\Expressions\ExpressionWalker;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class CountExpression extends AggregateExpression {
    private $UniqueValueExpressions;
    
    final public function __construct(array $UniqueValueExpressions = null) {
        $this->UniqueValueExpressions = $UniqueValueExpressions;
    }
    
    public function Traverse(ExpressionWalker $Walker) {
        if($this->UniqueValueExpressions === null) {
            return $this;
        }
        
        return $this->Update(
                $Walker->Walk($this->UniqueValueExpressions));
    }
    
    final public function UniqueValuesOnly() {
        return $this->UniqueValuesOnly;
    }
    
    /**
     * @return boolean
     */
    final public function HasValueExpressions() {
        return $this->UniqueValueExpressions != null;
    }
    
    /**
     * @return Expression[]|null
     */
    final public function GetValueExpressions() {
        return $this->UniqueValueExpressions;
    }
    
    final public function Simplify() {
        return $this->Update(
                self::SimplifyAll($this->UniqueValueExpressions));
    }
    
    final public function Update(array $UniqueValueExpressions = null) {
        if($this->UniqueValueExpressions === $UniqueValueExpressions) {
            return $this;
        }
        
        return new static($UniqueValueExpressions);
    }
}

?>