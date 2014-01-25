<?php

namespace Storm\Api\Base\Fluent;

use \Storm\Core\Object;
use \Storm\Api\Base\ClosureToASTConverter;
use \Storm\Drivers\Fluent\Object\Request;
use  \Storm\Drivers\Fluent\Object\Closure;

/**
 * The RequestBuilder provides a fluent interface for building requests
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class RequestBuilder extends CriterionBuilder {
    private $Properties;
    private $IsSingleEntity;
    
    public function __construct(
            Object\EntityMap $EntityMap, 
            ClosureToASTConverter $ClosureToASTConverter) {
        parent::__construct($EntityMap, $ClosureToASTConverter);
        
        $this->EntityMap = $EntityMap;
        $this->Properties = $this->EntityMap->GetProperties();
        $this->IsSingleEntity = false;
    }
    
    /**
     * Builds the request from specified parameters
     * 
     * @return Request
     */
    final public function BuildRequest() {
        return new Request(
            $this->EntityMap, 
            $this->Properties, 
            $this->IsSingleEntity, 
            $this->BuildCriterion());
    }
    
    /**
     * Sets the request to return only the first entity or null if 
     * none exists.
     * 
     * @return RequestBuilder
     */
    public function First() {
        $this->IsSingleEntity = true;
        $this->Limit(1);
        
        return $this;
    }
    
    /**
     * Sets the request to return the retrieved entities as an array
     * 
     * @return RequestBuilder
     */
    public function AsArray() {  
        $this->IsSingleEntity = false;
        
        return $this;
    }
}

?>