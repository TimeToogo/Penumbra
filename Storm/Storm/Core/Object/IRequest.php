<?php

namespace Storm\Core\Object;

/**
 * The request represents a range of entities to load specified by the criterion.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IRequest {
    const IRequestType = __CLASS__;
    
    /**
     * @return string
     */
    public function GetEntityType();
    
    /**
     * The properties to load.
     * 
     * @return IProperty[]
     */
    public function GetProperties();
    
    /**
     * @return ICriterion
     */
    public function GetCriterion();
}

?>