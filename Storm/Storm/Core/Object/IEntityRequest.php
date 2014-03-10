<?php

namespace Storm\Core\Object;

/**
 * The request represents a range of entities.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IEntityRequest extends IRequest {
    const IEntityRequestType = __CLASS__;
    
    /**
     * @return IProperty[]
     */
    public function GetProperties();
}

?>