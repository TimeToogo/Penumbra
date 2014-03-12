<?php

namespace Storm\Pinq;

use \Storm\Drivers\Base\Object;
use \Storm\Core\Object\IEntityMap;

abstract class Request extends Object\Request {
    
    use FunctionParsing;
    
    public function __construct(
            IEntityMap $EntityMap, 
            IFunctionToExpressionTreeConverter $FunctionToExpressionTreeConverter,
            array $GroupByFunctions = [], 
            array $LetVariableFunctions = [], 
            array $AggregatePredicateFunctions = [], 
            \Storm\Core\Object\ICriteria $Criteria = null) {
        $this->EntityMap = $EntityMap;
        $this->FunctionToExpressionTreeConverter = $FunctionToExpressionTreeConverter;
        
        $GroupByExpressions = array_map(
                function ($I) { return $this->ParseFunctionReturn($I, 'group by'); }, 
                $Groups);
                
        $AggregatePredicateExpressions = array_map(
                function ($I) { return $this->ParseFunctionReturn($I, 'aggregate predicate'); }, 
                $AggregatePredicateFunctions);
        
        parent::__construct(
                $EntityMap->GetEntityType(),
                $GroupByExpressions,
                $AggregatePredicateExpressions,
                $Criteria ?: new Criteria($EntityMap, $FunctionToExpressionTreeConverter));
    }
    
    private function ParseGroup(IGroup $Group) {
        
    }
}

?>
