<?php

namespace Penumbra\Core\Mapping;

use \Penumbra\Core\Containers\Map;
use \Penumbra\Core\Object;
use \Penumbra\Core\Relational;
use \Penumbra\Core\Object\Expressions;

/**
 * The interface representing an entity property mapped to a to one relation.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IEntityPropertyToOneRelationMapping extends IRelationshipPropertyRelationMapping {
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
     * This method should be implemented such that it sets the revival data of the mapped property
     * with the appropriate data to revive the related entity.
     * 
     * @param Relational\Database $Database The database to load the related rows from
     * @param Map $ResultRowRevivalDataMap The map containing the parent loaded result rows and 
     * respective revival data
     * @return void
     */
    public function Revive(Relational\Database $Database, array $ResultRowArray, array $RevivalDataArray);
    
    /**
     * This method should be implemented such that it saves the relationship between
     * the parent data and related data to the supplied transaction.
     * 
     * @param UnitOfWorkTransactionMapping $Mapping The transaction mapping context
     * @param Relational\ColumnData $ParentData The column data of the parent
     * @param Object\RelationshipChange $RelationshipChange The change in the relationship state
     * @return void
     */
    public function Persist(UnitOfWorkTransactionMapping $Mapping, Relational\ResultRow $ParentData, Object\RelationshipChange $RelationshipChange);
}

?>