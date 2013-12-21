<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;


final class RevivingContextOld extends MappingContext {
    private $LoadedRelationRows = array();
    private $RevivedEntities = array();
    
    public function __construct(DomainDatabaseMap $DomainDatabaseMap) {
        parent::__construct($DomainDatabaseMap);
    }
    
    public function GetRelationEntityType(Relational\IRelation $Relation) {
        return $this->GetDomainDatabaseMap()->GetRelationMapByTable($Relation->GetTable())->GetEntityType();
    }
    
    public function LoadToManyRelationRows(Relational\IToManyRelation $ToManyRelation, array $Rows) {
        $RelatedRowsArray = $this->GetRelatedRows($ToManyRelation, $Rows);
        $UnloadedRows = array_diff_key($Rows, $RelatedRowsArray);
        
        $NewRelatedRowsArray = $this->GetDomainDatabaseMap()->GetDatabase()->LoadToManyRelation($ToManyRelation, $UnloadedRows);
        foreach($NewRelatedRowsArray as $NewRelatedRows) {
            $this->CacheLoadedRelationRows($ToManyRelation, $NewRelatedRows);
        }
        
        return $RelatedRowsArray + $NewRelatedRowsArray;
    }
    
    public function LoadToOneRelationRows(Relational\IToOneRelation $ToOneRelation, array $Rows) {
        $RelatedRows = $this->GetRelatedRows($ToOneRelation, $Rows);
        $UnloadedRows = array_diff_key($Rows, $RelatedRows);
        
        $NewRelatedRows = $this->GetDomainDatabaseMap()->GetDatabase()->LoadToOneRelation($ToOneRelation, $UnloadedRows);
        $this->CacheLoadedRelationRows($ToOneRelation, $NewRelatedRows);
        
        return $RelatedRows + $NewRelatedRows;
    }
    
    private function GetRelatedRows(Relational\IRelation $Relation, array $Rows) {
        $RelatedRows = array();
        foreach($Rows as $Key => $Row) {
            $Hash = $this->GetRelationRowHash($Relation, $Row);
            if(isset($this->LoadedRelationRows[$Hash]))
                $RelatedRows[$Key] = $this->LoadedRelationRows[$Hash];
        }
        
        return $RelatedRows;
    }
    
    private function CacheLoadedRelationRows(Relational\IRelation $Relation, array $Rows) {
        foreach($Rows as $Row) {
            $Hash = $this->GetRelationRowHash($Relation, $Row);
            $this->LoadedRelationRows[$Hash] = $Row;
        }
    }
    
    private function GetRelationRowHash(Relational\IRelation $Relation, Relational\Row $Row) {
        return spl_object_hash($Relation) . $Row->GetPrimaryKey()->Hash();
    }
    
    public function ReviveEntities(array $RelatedRows) {
        $RevivedEntities = $this->GetRevivedEntities($RelatedRows);
        $UnloadedRows = array_diff_key($RelatedRows, $RevivedEntities);
        
        $NewEntities = $this->GetDomainDatabaseMap()->ReviveEntities($this, $UnloadedRows);
        $this->CacheRevivedEntities($UnloadedRows, $NewEntities);
        
        return $RevivedEntities + $NewEntities;
    }
    
    private function GetRevivedEntities(array $RelatedRows) {
        $RevivedEntities = array();
        foreach ($RelatedRows as $Key => $RelatedRow) {
            $RowHash = $RelatedRow->GetPrimaryKey()->Hash();
            if(isset($this->RevivedEntities[$RowHash]))
                $RevivedEntities[$Key] = $this->RevivedEntities[$RowHash];
        }
        return $RevivedEntities;        
    }
    
    private function CacheRevivedEntities(array $Rows, array $RevivedEntities) {
        foreach($Rows as $Key => $Row) {
            $this->RevivedEntities[$Row->GetPrimaryKey()->Hash()] = $RevivedEntities[$Key];
        }
    }
    
    public function ReviveEntityInstances(Map $RowInstanceMap) {
        return $this->GetDomainDatabaseMap()->ReviveEntityInstances($this, $RowInstanceMap);
    }
}

?>