<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Object;
use \Storm\Core\Relational;

/**
 * The interface representing an collectioon property mapped to a to many relation.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface ICollectionPropertyToManyRelationMapping extends IRelationshipPropertyRelationMapping {
    const ICollectionPropertyToManyRelationMappingType = __CLASS__;
    
    /**
     * The mapped collection property.
     * 
     * @return Object\ICollectionProperty
     */
    public function GetCollectionProperty();
    
    /**
     * The mapped to many relation.
     * 
     * @return Relational\IToManyRelation
     */
    public function GetToManyRelation(); 
        
    /**
     * This method should be implemented such that it sets the revival data of the mapped property
     * with the appropriate data to revive the related entity.
     * 
     * @param DomainDatabaseMap $DomainDatabaseMap The parent domain database map
     * @param Map $ResultRowRevivalDataMap The map containing the parent loaded result rows and 
     * respective revival data
     * @return void
     */
    public function Revive(DomainDatabaseMap $DomainDatabaseMap, array $ResultRowArray, array $RevivalDataArray);
    
    /**
     * This method should be implemented such that it saves the relationships between
     * the parent data and related data in the supplied transaction.
     * 
     * @param Relational\Transaction $Transaction The transaction context
     * @param array $ParentData The column data of the parent
     * @param Relational\RelationshipChange[] $RelationshipChanges The change in the relationship state
     * @return void
     */
    public function Persist(Relational\Transaction $Transaction, Relational\ResultRow $ParentData, array $RelationshipChanges);
}

?>