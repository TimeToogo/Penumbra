<?php

namespace Storm\Core\Object\Expressions\Aggregates;

use \Storm\Core\Object\IProperty;

/**
 * Expression for an aggregate function.
 * Count, Maximum, Minimum, Average, Sum, Implode, All, Any
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class ValueAggregateExpression extends AggregateExpression {
    private $ValueExpression;
    
    public function __construct($UniqueValuesOnly, Expression $ValueExpression) {
        parent::__construct($UniqueValuesOnly);
        $this->ValueExpression = $ValueExpression;
    }

    /**
     * @return Expression
     */
    final public function GetValueExpression() {
        return $this->ValueExpression;
    }
    
    final protected function MatchesValueAggregate($UniqueValuesOnly, Expression $ValueExpression) {
        return $this->MatchesAggregate($UniqueValuesOnly)
                && $this->ValueExpression = $ValueExpression;
    }
}

?>