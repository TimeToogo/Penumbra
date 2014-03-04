<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Object;
use \Storm\Core\Relational;
use \Storm\Core\Object\Expressions\TraversalExpression;

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
     * @return Relational\Expression[]
     */
    public function MapTraversalExpression(Relational\Criterion $Criterion, TraversalExpression $TraversalExpression);
}

?>