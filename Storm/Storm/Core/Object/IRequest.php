<?php

namespace Storm\Core\Object;

interface IRequest {
    const IRequestType = __CLASS__;
    
    public function GetEntityType();
    
    /**
     * @return IProperty[]
     */
    public function GetProperties();
    
    /**
     * @return ICriterion
     */
    public function GetCriterion();
}

?>