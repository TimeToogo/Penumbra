<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;

abstract class ToManyRelationBase extends KeyedRelation implements Relational\IToManyRelation {
    
    final public function MapParentKeysToRelatedRows(array $ParentRows, array $RelatedRows) {
        $MappedRelatedRows = array();
        if(count($ParentRows) === 1) {
            $MappedRelatedRows[key($ParentRows)] = $RelatedRows;
        } 
        else {
            $this->GroupRelatedRowsByParentKeys($MappedRelatedRows, $this->GetForeignKey(), $ParentRows, $RelatedRows);
        }
        
        return $MappedRelatedRows;
    }
    protected abstract function GroupRelatedRowsByParentKeys(array &$MappedRelatedRows, ForeignKey $ForeignKey, array $ParentRows, array $RelatedRows);
        
    final protected function GroupRowsByColumnValues(array $ResultRows, array $Columns) {
        $GroupedRelatedRows = array();
        $GroupByKeys = Relational\ResultRow::GetAllDataFromColumns($ResultRows, $Columns);
        foreach($ResultRows as $Key => $ResultRow) {
            $Hash = $GroupByKeys[$Key]->HashData();
            if(!isset($GroupedRelatedRows[$Hash])) {
                $GroupedRelatedRows[$Hash] = array();
            }
            $GroupedRelatedRows[$Hash][] = $ResultRow;
        }
        
        return $GroupedRelatedRows;
    }
    
    final protected function MapParentRowKeysToGroupedRelatedRows(array &$MappedRelatedRows, array $ParentRows, array $MapByColumns, array $GroupedRelatedRows) {
        $ParentDataHashKeyMap = $this->MakeHashedDataToKeyMap($ParentRows, $MapByColumns);
        foreach($ParentDataHashKeyMap as $HashedData => $ParentKey) {
            if(isset($GroupedRelatedRows[$HashedData])) {
                $MappedRelatedRows[$ParentKey] = $GroupedRelatedRows[$HashedData];
            }
            else {
                $MappedRelatedRows[$ParentKey] = array();
            }
        }
    }
    
    public function Persist(Relational\Transaction $Transaction, 
            Relational\ResultRow $ParentData, array $RelationshipChanges) {
        $IdentifyingChildRows = array();
        $Table = $this->GetTable();
        $ForeignKey = $this->GetForeignKey();
        foreach($RelationshipChanges as $RelationshipChange) {
            
            if($RelationshipChange->HasPersistedRelationship()) {
                $PersistedRelationship = $RelationshipChange->GetPersistedRelationship();
                
                if($PersistedRelationship->IsIdentifying()) {
                    $IdentifyingChildRows[] = $PersistedRelationship->GetChildResultRow()->GetRow($Table);
                }
            }
            
            if($RelationshipChange->HasDiscardedRelationship()) {
                $DiscardedRelationship = $RelationshipChange->GetDiscardedRelationship();
                
                if($DiscardedRelationship->IsIdentifying()) {
                    $PrimaryKeyToDiscard = $DiscardedRelationship->GetRelatedPrimaryKey();
                    if($this->IsInversed()) {
                        $ForeignKey->MapReferencedToParentKey($ParentData, $PrimaryKeyToDiscard);
                    }
                    else {
                        $ForeignKey->MapReferencedToParentKey($ParentData, $PrimaryKeyToDiscard);
                    }
                    $Transaction->Discard($PrimaryKeyToDiscard);
                }
            }
        }
        $ParentRow = $ParentData->GetRow($this->GetParentTable());
        if(count($IdentifyingChildRows) > 0) {
            $this->PersistIdentifyingRelationship($Transaction, $ParentRow, $IdentifyingChildRows);
        }
    }
    protected abstract function PersistIdentifyingRelationship(Relational\Transaction $Transaction, 
            Relational\Row $ParentRow, array $ChildRows);
    
}

?>