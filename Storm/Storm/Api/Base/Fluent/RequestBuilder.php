<?php

namespace Storm\Api\Base\Fluent;

use \Storm\Core\Object;
use \Storm\Drivers\Fluent\Object\Request;
use  \Storm\Drivers\Fluent\Object\Closure;

class RequestBuilder extends CriterionBuilder {
    private $Properties;
    private $IsSingleEntity;
    
    public function __construct(
            Object\EntityMap $EntityMap, 
            Closure\ClosureToASTConverter $ClosureToASTConverter) {
        parent::__construct($EntityMap, $ClosureToASTConverter);
        
        $this->EntityMap = $EntityMap;
        $this->Properties = $this->EntityMap->GetProperties();
        $this->IsSingleEntity = false;
    }
    
    final public function BuildRequest() {
        return new Request(
            $this->EntityMap, 
            $this->Properties, 
            $this->IsSingleEntity, 
            $this->BuildCriterion());
    }
    
    public function First() {
        $this->IsSingleEntity = true;
        $this->Limit(1);
        
        return $this;
    }
    
    public function AsArray() {  
        $this->IsSingleEntity = false;
        
        return $this;
    }
}

?>