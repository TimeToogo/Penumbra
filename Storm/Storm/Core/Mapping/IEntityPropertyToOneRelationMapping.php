<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Object;
use \Storm\Core\Relational;
use \Storm\Core\Object\Expressions;

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
     * the parent data and related data in the supplied transaction.
     * 
     * @param Relational\Transaction $Transaction The transaction context
     * @param Relational\ColumnData $ParentData The column data of the parent
     * @param Relational\RelationshipChange $RelationshipChange The change in the relationship state
     * @return void
     */
    public function Persist(Relational\Transaction $Transaction, Relational\ResultRow $ParentData, Relational\RelationshipChange $RelationshipChange);
    
    /**
     * @return Relational\Expressions\Expression[]
     */
    public function MapObjectOperation(Relational\Criterion $Criterion, Expressions\ObjectOperationExpression $ObjectOperationExpression);
    
    /**
     * @return Relational\Expressions\Expression[]
     */
    public function MapAssignment(Relational\Criterion $Criterion, Expressions\Expression $AssignmentValueExpression);
    
    /**
     * @return Relational\Expressions\Expression[]
     */
    public function MapBinary(Relational\Criterion $Criterion, Expressions\Expression $OperandValueExpression);
}

?>