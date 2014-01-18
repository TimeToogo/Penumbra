<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;

/**
 * The interface representing an entity property mapped to a to one relation.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IEntityPropertyToOneRelationMapping extends IPropertyMapping {
    const IEntityPropertyToOneRelationMappingType = __CLASS__;
    
    /**
     * The mapped entity property.
     * 
     * @return Object\IEntityProperty
     */
    public function GetEntityProperty();
    
    /**
     * The mapped to one relation.
     * 
     * @return Relational\IToOneRelation
     */
    public function GetToOneRelation();
    
    /**
     * Adds any constraints and/or properties to the reqeuest required for loading the relation.
     * 
     * @param DomainDatabaseMap $DomainDatabaseMap The parent domain database map
     * @param Relational\Request $RelationalRequest The request to map to
     * @return void
     */
    public function AddToRelationalRequest(DomainDatabaseMap $DomainDatabaseMap, Relational\Request $RelationalRequest);
    
    /**
     * This method should be implemented such that it sets the revival data of the mapped property
     * with the appropriate data to revive the related entity.
     * 
     * @param \StDomainDatabaseMap $DomainDatabaseMap The parent domain database map
     * @param Map $ResultRowRevivalDataMap The map containing the parent loaded result rows and 
     * respective revival data
     * @return void
     */
    public function Revive(DomainDatabaseMap $DomainDatabaseMap, Map $ResultRowRevivalDataMap);
    
    /**
     * This method should be implemented such that it saves the relationship between
     * the parent data and related data in the supplied transaction.
     * 
     * @param Relational\Transaction $Transaction The transaction context
     * @param Relational\ColumnData $ParentData The column data of the parent
     * @param Relational\RelationshipChange $RelationshipChange The change in the relationship state
     * @return void
     */
    public function Persist(Relational\Transaction $Transaction, Relational\ColumnData $ParentData, Relational\RelationshipChange $RelationshipChange);
}

?>