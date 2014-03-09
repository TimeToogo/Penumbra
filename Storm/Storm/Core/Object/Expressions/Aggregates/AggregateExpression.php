<?php

namespace Storm\Core\Object\Expressions\Aggregates;

use \Storm\Core\Object\IProperty;

/**
 * Expression for an aggregate function.
 * Count, Maximum, Minimum, Average, Sum, Implode, All, Any
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class AggregateExpression extends Expression {
    private $UniqueValuesOnly;
    
    public function __construct($UniqueValuesOnly) {
        $this->UniqueValuesOnly = $UniqueValuesOnly;
    }
    
    final public function UniqueValuesOnly() {
        return $this->UniqueValuesOnly;
    }
    
    final protected function MatchesAggregate($UniqueValuesOnly) {
        return $this->UniqueValuesOnly === $UniqueValuesOnly;
    }
}

?>