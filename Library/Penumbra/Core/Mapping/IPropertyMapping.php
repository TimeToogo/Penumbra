<?php

namespace Penumbra\Core\Mapping;

use \Penumbra\Core\Object;
use \Penumbra\Core\Relational;
use \Penumbra\Core\Object\Expressions as O;

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
     * Adds the necessary constraints and/or columns to the select for loading this property mapping.
     * 
     * @param Relational\ResultSetSelect $Select
     * @return void
     */
    public function AddLoadingRequirementsToSelect(Relational\ResultSetSelect $Select);
    
    /**
     * Adds the necessary constraints to the result set for querying this property mapping.
     * 
     * @param Relational\ResultSetSpecification $ResultSetSpecification
     * @return void
     */
    public function AddTraversalRequirementsToResultSet(Relational\ResultSetSpecification $ResultSetSpecification);
    
    /**
     * @return Relational\Expression
     */
    public function MapPropertyExpression(Relational\ResultSetSources $Sources, &$ReturnType);
}

?>