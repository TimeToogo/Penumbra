<?php

namespace Storm\Core\Object;

/**
 * The request represents a set of data to load.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IDataRequest extends IRequest {
    const IDataRequestType = __CLASS__;
    
    /**
     * @return array<string, Expression>
     */
    public function GetAliasExpressionMap();
}

?>