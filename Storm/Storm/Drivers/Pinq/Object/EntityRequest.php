<?php

namespace Storm\Drivers\Pinq\Object;

use \Storm\Core\Object;
use \Storm\Drivers\Pinq\Object\Functional;
use \Storm\Core\Object\Expressions as O;

class EntityRequest extends Request implements Object\IEntityRequest {
    private $Properties;
    
    public function __construct(
            Object\IEntityMap $EntityMap, 
            IFunctionToExpressionTreeConverter $FunctionToExpressionTreeConverter, 
            array $Properties = null,
            array $GroupByFunctions = [], 
            array $AggregatePredicateFunctions = [], 
            \Storm\Core\Object\ICriterion $Criterion = null) {
        
        $this->Properties = $Properties ?: $EntityMap->GetProperties();
        
        parent::__construct(
                $EntityMap, 
                $FunctionToExpressionTreeConverter, 
                $GroupByFunctions, 
                $AggregatePredicateFunctions, 
                $Criterion);
    }

    final public function GetProperties() {
        return $this->Properties;
    }
}

?>
