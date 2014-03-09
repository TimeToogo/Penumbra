<?php

namespace Storm\Drivers\Pinq\Object;

use \Storm\Drivers\Base\Object;
use \Storm\Core\Object\IEntityMap;
use \Storm\Drivers\Pinq\Object\Functional;

class Request extends Object\Request {
    
    use ReturnExpression;
    
    public function __construct(
            IEntityMap $EntityMap, 
            array $Properties = null, 
            array $GroupByExpressionTrees = array(), 
            array $AggregatePredicateExpressionTrees = array(), 
            $IsSingleEntity = false,
            \Storm\Core\Object\ICriterion $Criterion = null) {
        $EntityType = $EntityMap->GetEntityType();
        
        $GroupByExpressions = array_map(
                function ($I) { return $this->ParseReturnExpression($I, 'group by'); }, 
                $GroupByExpressionTrees);
                
        $AggregatePredicateExpressions = array_map(
                function ($I) { return $this->ParseReturnExpression($I, 'aggregate predicate'); }, 
                $AggregatePredicateExpressionTrees);
        
        parent::__construct(
                $EntityType,
                $Properties ?: $EntityMap->GetProperties(),
                $GroupByExpressions,
                $AggregatePredicateExpressions,
                $IsSingleEntity,
                $Criterion ?: new Criterion($EntityType));
    }
}

?>
