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
        
    final protected function GroupRowsByColumnValues(array $ResultRows, array $ColumnIdentifiers) {
        $GroupedRelatedRows = array();
        
        if(count($ColumnIdentifiers) === 1) {
            $ColumnIdentifier = reset($ColumnIdentifiers);
            
            foreach($ResultRows as $Key => &$ResultRow) {
                $Hash = md5(json_encode($ResultRow[$ColumnIdentifier]));
                
                if(!isset($GroupedRelatedRows[$Hash])) {
                     $GroupedRelatedRows[$Hash] = array();
                }
                $GroupedRelatedRows[$Hash][$Key] =& $ResultRow;
            }
        }
        else {
            $ColumnIdentifiers = array_flip(array_values($ColumnIdentifiers));
            
            foreach($ResultRows as $Key => &$ResultRow) {
                $HashValues = array_intersect_key($ResultRow, $ColumnIdentifiers);
                ksort($HashValues);
                $Hash = md5(json_encode(array_values($HashValues)));
                
                if(!isset($GroupedRelatedRows[$Hash])) {
                     $GroupedRelatedRows[$Hash] = array();
                }
                $GroupedRelatedRows[$Hash][$Key] =& $ResultRow;
            }
        }
        
        return $GroupedRelatedRows;
    }
    
    public function Persist(Relational\Transaction $Transaction, 
            array $ParentData, array $RelationshipChanges) {
        $IdentifyingChildRows = array();
        $ParentTable = $this->GetParentTable();
        $RelatedTable = $this->GetTable();
        
        foreach($RelationshipChanges as $RelationshipChange) {
            
            if($RelationshipChange->HasPersistedRelationship()) {
                $PersistedRelationship = $RelationshipChange->GetPersistedRelationship();
                if($PersistedRelationship->IsIdentifying()) {
                    $IdentifyingChildRows[] =& $RelatedTable->GetRowData($PersistedRelationship->GetChildResultRow());
                }
            }
            
            if($RelationshipChange->HasDiscardedRelationship()) {
                $DiscardedRelationship = $RelationshipChange->GetDiscardedRelationship();
                
                if($DiscardedRelationship->IsIdentifying()) {
                    $PrimaryKeyToDiscard = $DiscardedRelationship->GetRelatedPrimaryKey();
                    $this->GetForeignKey()->MapReferencedToParentKey($ParentData, $PrimaryKeyToDiscard);
                    $Transaction->Discard($RelatedTable, $PrimaryKeyToDiscard);
                }
            }
        }
        
        if(count($IdentifyingChildRows) > 0) {
            $this->PersistIdentifyingRelationship($Transaction, $ParentData, $IdentifyingChildRows);
        }
    }
    protected abstract function PersistIdentifyingRelationship(Relational\Transaction $Transaction, 
            array $ParentRow, array &$ChildRows);
    
}

?>