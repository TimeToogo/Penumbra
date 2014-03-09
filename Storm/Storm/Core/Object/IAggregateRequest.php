<?php

namespace Storm\Core\Object;

/**
 * The aggregate request represents calculated value(s) from a set of entities
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IAggregateRequest extends IRequest {
    const IAggregateRequestType = __CLASS__;
    
    /**
     * The aggregates to calculate should maintain index on load.
     * 
     * @return array<string, Expressions\Aggregates\AggregateExpression>
     */
    public function GetAggregateExpressions();
}

?>