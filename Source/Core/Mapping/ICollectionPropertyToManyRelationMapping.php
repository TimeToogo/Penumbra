<?php

namespace Penumbra\Core\Mapping;

use \Penumbra\Core\Containers\Map;
use \Penumbra\Core\Object;
use \Penumbra\Core\Relational;

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
     * with the appropriate data to revive the related entities.
     * 
     * @param Relational\Database $Database The database to load the related rows from
     * @param Relational\ResultRow[] $ResultRowArray The loaded result rows
     * @param Relational\RevivalData[] $RevivalDataArray The revival data array
     * @return void
     */
    public function Revive(Relational\Database $Database, array $ResultRowArray, array $RevivalDataArray);
    
    /**
     * This method should be implemented such that it saves the relationships between
     * the parent data and related data in the supplied transaction.
     * 
     * @param UnitOfWorkTransactionMapping $Mapping The transaction mapping context
     * @param array $ParentData The column data of the parent
     * @param Object\RelationshipChange[] $RelationshipChanges The change in the relationship state
     * @return void
     */
    public function Persist(UnitOfWorkTransactionMapping $Mapping, Relational\ResultRow $ParentData, array $RelationshipChanges);
}

?>