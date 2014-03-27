<?php

namespace Penumbra\Core\Object;

/**
 * The procedure represents a set of assigments that should be carried out upon
 * a set entities defined by the criteria.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IQuery {
    const IQueryType = __CLASS__;
    
    /**
     * @return string
     */
    public function GetEntityType();
    
    /**
     * @return boolean
     */
    public function IsFromEntityRequest();
    
    /**
     * @return IEntityRequest|null
     */
    public function GetFromEntityRequest();
}

?>