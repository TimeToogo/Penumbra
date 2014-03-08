<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Object;
use \Storm\Core\Relational;
use \Storm\Core\Object\Expressions as O;

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
     * Adds the necessary constraints to the criterion.
     * 
     * @return void
     */
    public function AddToCriterion(Relational\Criterion $Criterion);
    
    /**
     * @return Relational\Expression
     */
    public function MapPropertyExpression();
}

?>