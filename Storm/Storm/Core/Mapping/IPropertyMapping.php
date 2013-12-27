<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Object;
use \Storm\Core\Relational;

interface IPropertyMapping {
    const IPropertyMappingType = __CLASS__;
    
    /**
     * @return Object\IProperty
     */
    public function GetProperty();
}

?>