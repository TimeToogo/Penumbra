<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;

abstract class ToOneRelationBase extends KeyedRelation implements Relational\IToOneRelation {
    
    final public function MapParentKeysToRelatedRow(array $ParentRows, array $RelatedRows) {
        if(count($RelatedRows) > count($ParentRows)) {
            throw new \Exception;//TODO: error message
        }
        if(count($RelatedRows) === 0) {
            return array();
        }
        
        if(count($ParentRows) === 1) {
            return [key($ParentRows) => reset($RelatedRows)];
        } 
        else {
            $MappedRelatedRows = array();
            $ForeignKey = $this->GetForeignKey();
            
            
            if($this->IsInversed()) {
                $ParentRowColumns = $ForeignKey->GetReferencedColumnIdentifiers();
                $RelatedRowColumns = $ForeignKey->GetParentColumnIdentifiers();
            }
            else {
                $ParentRowColumns = $ForeignKey->GetParentColumnIdentifiers();
                $RelatedRowColumns = $ForeignKey->GetReferencedColumnIdentifiers();
            }
            
            $HashedParentRowToKeyMap = $this->MakeHashedDataToKeyMap($ParentRows, $ParentRowColumns);
            $KeyedRelatedRows = $this->IndexRowsByHashedColumnValues($RelatedRows, $RelatedRowColumns);
            
            foreach($HashedParentRowToKeyMap as $HashedData => $Key) {
                $MappedRelatedRows[$Key] = isset($KeyedRelatedRows[$HashedData]) ? 
                        $KeyedRelatedRows[$HashedData] : null;
            }
            return $MappedRelatedRows;
        }
        
    }
    
    public function Persist(Relational\Transaction $Transaction, 
            array &$ParentData, Relational\RelationshipChange $RelationshipChange) {
        
        if($RelationshipChange->HasPersistedRelationship()) {
            $PersistedRelationship = $RelationshipChange->GetPersistedRelationship();
            
            if($PersistedRelationship->IsIdentifying()) {
                $ChildResultRow =& $PersistedRelationship->GetChildResultRow();
                $ChildRow =& $this->GetTable()->GetRowData($ChildResultRow);
                $this->PersistIdentifyingRelationship($Transaction, $ParentData, $ChildRow);
            }
        }
    }
    
    final protected function PersistIdentifyingRelationship(
            Relational\Transaction $Transaction, 
            array &$ParentRow, array &$ChildResultRow) {
        $ParentTable = $this->GetParentTable();
        $RelatedTable = $this->GetTable();
        if($this->IsInversed()) {
            if($ParentTable->HasPrimaryKeyData($ParentRow)) {
                $this->GetForeignKey()->MapReferencedToParentKey($ParentRow, $ChildResultRow);
            }
            else {
                $Transaction->SubscribeToPrePersistEvent($RelatedTable, 
                        function () use (&$ParentRow, &$ChildResultRow) {
                            $this->GetForeignKey()->MapReferencedToParentKey($ParentRow, $ChildResultRow);
                        });
            }
        }
        else {
            if($ParentTable->HasPrimaryKeyData($ParentRow)) {
                $this->GetForeignKey()->MapParentToReferencedKey($ParentRow, $ChildResultRow);
            }
            else {
                $Transaction->SubscribeToPrePersistEvent($RelatedTable, 
                        function () use (&$ParentRow, &$ChildResultRow) {
                            $this->GetForeignKey()->MapParentToReferencedKey($ParentRow, $ChildResultRow);
                        });
            }
        }
    }
    
}

?>