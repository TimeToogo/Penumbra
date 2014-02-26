<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Object;
use \Storm\Core\Relational;

/**
 * The interface representing an entity property mapped to a to one relation.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IRelationshipPropertyRelationMapping extends IPropertyMapping {
    const IRelationshipPropertyRelationMapping = __CLASS__;
    
    /**
     * The mapped relationship property.
     * 
     * @return Object\IRelationshipProperty
     */
    public function GetRelationshipProperty();
    
    /**
     * The mapped relation.
     * 
     * @return Relational\IRelation
     */
    public function GetRelation();
    
    /**
     * Adds any constraints and/or properties to the reqeuest required for loading the relation.
     * 
     * @param DomainDatabaseMap $DomainDatabaseMap The parent domain database map
     * @param Relational\Request $RelationalRequest The request to map to
     * @return void
     */
    public function AddToRelationalRequest(DomainDatabaseMap $DomainDatabaseMap, Relational\Request $RelationalRequest);
    
    
    
}

?>