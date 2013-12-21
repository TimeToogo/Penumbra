<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;


final class RevivingContext extends MappingContext {
    public function __construct(DomainDatabaseMap $DomainDatabaseMap) {
        parent::__construct($DomainDatabaseMap);
    }
    
    public function GetRelationEntityType(Relational\IRelation $Relation) {
        return $this->GetDomainDatabaseMap()->GetRelationMapByTable($Relation->GetTable())->GetEntityType();
    }
    
    public function LoadToManyRelationRows(Relational\IToManyRelation $ToManyRelation, array $Rows) {
        $RelatedRowsArray = $this->GetDomainDatabaseMap()->GetDatabase()->LoadToManyRelation($ToManyRelation, $Rows);
        
        return $RelatedRowsArray;
    }
    
    public function LoadToOneRelationRows(Relational\IToOneRelation $ToOneRelation, array $Rows) {
        $RelatedRows = $this->GetDomainDatabaseMap()->GetDatabase()->LoadToOneRelation($ToOneRelation, $Rows);
        
        return $RelatedRows;
    }
    
    public function ReviveEntities($EntityType, array $RelatedRows) {
        $Entities = $this->GetDomainDatabaseMap()->ReviveEntities($EntityType, $this, $RelatedRows);
        
        return $Entities;
    }
    
    public function ReviveEntityInstances(Map $RowInstanceMap) {
        return $this->GetDomainDatabaseMap()->ReviveEntityInstances($this, $RowInstanceMap);
    }
}

?>