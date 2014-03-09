<?php

namespace Storm\Core\Object\Expressions\Aggregates;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ImplodeExpression extends ValueAggregateExpression {
    private $Seperator;
    public function __construct($UniqueValuesOnly, Expression $ValueExpression) {
        parent::__construct($UniqueValuesOnly, $ValueExpression);
    }

}

?>