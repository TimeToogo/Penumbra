<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;

interface IEntityPropertyToOneRelationMapping extends IPropertyMapping {
    const IEntityPropertyToOneRelationMappingType = __CLASS__;
    
    /**
     * @return Object\IEntityProperty
     */
    public function GetEntityProperty();
    
    /**
     * @return Relational\IToOneRelation
     */
    public function GetToOneRelation();
    
    public function AddToRelationalRequest(DomainDatabaseMap $DomainDatabaseMap, Relational\Request $RelationalRequest);
    public function Revive(DomainDatabaseMap $DomainDatabaseMap, Map $ResultRowRevivalDataMap);
    public function Persist(Relational\Transaction $Transaction, Relational\ColumnData $ParentData, Relational\RelationshipChange $RelationshipChange);
}

?>