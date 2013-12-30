<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;

interface ICollectionPropertyToManyRelationMapping extends IPropertyMapping {
    const ICollectionPropertyToManyRelationMappingType = __CLASS__;
    
    /**
     * @return Object\ICollectionProperty
     */
    public function GetCollectionProperty();
    
    /**
     * @return Relational\IToManyRelation
     */
    public function GetToManyRelation();    
    
    public function AddToRelationalRequest(DomainDatabaseMap $DomainDatabaseMap, Relational\Request $RelationalRequest);
    public function Revive(DomainDatabaseMap $DomainDatabaseMap, Map $ResultRowRevivalDataMap);
    public function Persist(Relational\Transaction $Transaction, Relational\ColumnData $ParentData, array $RelationshipChanges);
}

?>