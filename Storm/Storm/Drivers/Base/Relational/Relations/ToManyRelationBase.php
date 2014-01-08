<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;

abstract class ToManyRelationBase extends KeyedRelation implements Relational\IToManyRelation {    
    final protected function NewRelationRequest() {
        return new Relational\Request(array(), false);
    }    
    
    final public function MapParentToRelatedRows(array $ParentRows, array $RelatedRows) {
        $Map = new Map();
        if(count($ParentRows) === 1) {
            $Map->Map(reset($ParentRows), new \ArrayObject($RelatedRows));
        } 
        else {
            $this->FillParentToRelatedRowsMap($Map, $this->GetForeignKey(), $ParentRows, $RelatedRows);
        }
        
        return $Map;
    }
    protected abstract function FillParentToRelatedRowsMap(Map $Map, ForeignKey $ForeignKey, array $ParentRows, array $RelatedRows);
    
    final protected function GroupRowsByColumns(array $RelatedRows, array $GroupByColumns) {
        $GroupedRelatedRows = array();
        $GroupByKeys = Relational\ResultRow::GetAllDataFromColumns($RelatedRows, $GroupByColumns);
        foreach($RelatedRows as $Key => $RelatedRow) {
            $Hash = $GroupByKeys[$Key]->HashData();
            if(!isset($GroupedRelatedRows[$Hash])) {
                $GroupedRelatedRows[$Hash] = array();
            }
            $GroupedRelatedRows[$Hash][] = $RelatedRow;
        }
        
        return $GroupedRelatedRows;
    }
    
    final protected function MapParentRowsToGroupedRelatedRows(Map $Map, array $ParentRows, array $MapByColumns, array $GroupedRelatedRows) {
        $MapByKeys = Relational\ResultRow::GetAllDataFromColumns($ParentRows, $MapByColumns);
        foreach($ParentRows as $Key => $ParentRow) {
            $Hash = $MapByKeys[$Key]->HashData();
            if(isset($GroupedRelatedRows[$Hash])) {
                $Map->Map($ParentRow, new \ArrayObject($GroupedRelatedRows[$Hash]));
            }
            else {
                $Map->Map($ParentRow, new \ArrayObject(array()));
            }
        }
    }
    
    public function Persist(Relational\Transaction $Transaction, 
            Relational\ResultRow $ParentData, array $RelationshipChanges) {
        $IdentifyingChildRows = array();
        $Table = $this->GetTable();
        foreach($RelationshipChanges as $RelationshipChange) {
            if($RelationshipChange->HasPersistedRelationship()) {
                $PersistedRelationship = $RelationshipChange->GetPersistedRelationship();
                if($PersistedRelationship->IsIdentifying()) {
                    $IdentifyingChildRows[] = $PersistedRelationship->GetChildResultRow()->GetRow($Table);
                }
            }
        }
        $ParentRow = $ParentData->GetRow($this->GetParentTable());
        $this->PersistIdentifyingRelationship($Transaction, $ParentRow, $IdentifyingChildRows);
    }
    protected abstract function PersistIdentifyingRelationship(Relational\Transaction $Transaction, 
            Relational\Row $ParentRow, array $ChildRows);
    
}

?>