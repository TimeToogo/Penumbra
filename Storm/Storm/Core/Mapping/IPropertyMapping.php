<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Object;
use \Storm\Core\Relational;

/**
 * The interface representing a mapped property
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IPropertyMapping {
    const IPropertyMappingType = __CLASS__;
        
    /**
     * The mapped property.
     * 
     * @return Object\IProperty
     */
    public function GetProperty();
    
    /**
     * @return Relational\Expressions\SetExpression[]
     */
    public function MapAssignmentExpression(Object\Expressions\AssignmentExpression $AssignmentExpression);    
}

?>