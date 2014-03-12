<?php

namespace Storm\Pinq;

use \Storm\Core\Object;
use \Storm\Pinq\Functional;
use \Storm\Core\Object\Expressions as O;

class EntityRequest extends Request implements Object\IEntityRequest {
    private $Properties;
    
    public function __construct(
            Object\IEntityMap $EntityMap, 
            IFunctionToExpressionTreeConverter $FunctionToExpressionTreeConverter, 
            array $Properties = null,
            array $GroupByFunctions = [], 
            array $AggregatePredicateFunctions = [], 
            \Storm\Core\Object\ICriteria $Criteria = null) {
        
        $this->Properties = $Properties ?: $EntityMap->GetProperties();
        
        parent::__construct(
                $EntityMap, 
                $FunctionToExpressionTreeConverter, 
                $GroupByFunctions, 
                $AggregatePredicateFunctions, 
                $Criteria);
    }

    final public function GetProperties() {
        return $this->Properties;
    }
}

?>
