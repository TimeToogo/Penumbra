<?php

namespace Storm\Drivers\Base\Object;

use \Storm\Core\Object;
use \Storm\Core\Object\Expressions\Expression;

class EntityRequest extends Request implements Object\IEntityRequest {
    private $Properties = [];
    
    public function __construct(
            $EntityOrType, 
            array $Properties, 
            array $GroupByExpressions,
            array $AggregatePredicateExpressions,
            Object\ICriteria $Criteria = null,
            Object\IEntityRequest $SubEntityRequest = null) {
        parent::__construct(
                $EntityOrType, 
                $GroupByExpressions, 
                $AggregatePredicateExpressions, 
                $Criteria,
                $SubEntityRequest);
        
        foreach($Properties as $Property) {
            $this->AddProperty($Property);
        }
    }
    
    final protected function AddProperty(Object\IProperty $Property) {
        $this->Properties[$Property->GetIdentifier()] = $Property;
    }
    
    final public function GetProperties() {
        return $this->Properties;
    }
}

?>