<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;

abstract class ToOneRelationBase extends KeyedRelation implements Relational\IToOneRelation {
    
    protected function JoinType() {
        return Relational\JoinType::Left;
    }
    
    final public function MapParentKeysToRelatedRow(array $ParentRows, array $RelatedRows) {
        if(count($RelatedRows) === 0) {
            return [];
        }

        
        if(count($ParentRows) === 1) {
            return [key($ParentRows) => reset($RelatedRows)];
        }
        else {
            $MappedRelatedRows = [];
            
            $ForeignKey = $this->GetForeignKey();
            if($this->IsInversed()) {
                $ParentRowColumns = $ForeignKey->GetReferencedColumns();
                $RelatedRowColumns = $ForeignKey->GetParentColumns();
            }
            else {
                $ParentRowColumns = $ForeignKey->GetParentColumns();
                $RelatedRowColumns = $ForeignKey->GetReferencedColumns();
            }
            
            $HashedParentRowToKeyMap = $this->MakeHashedDataToKeyMap($ParentRows, $ParentRowColumns);
            $KeyedRelatedRows = $this->IndexRowsByHashedColumnValues($RelatedRows, $RelatedRowColumns);
            
            foreach($HashedParentRowToKeyMap as $HashedData => $ParentKey) {
                $MappedRelatedRows[$ParentKey] = isset($KeyedRelatedRows[$HashedData]) ? 
                        $KeyedRelatedRows[$HashedData] : null;
            }
            return $MappedRelatedRows;
        }
    }
    
    public function Persist(Relational\Transaction $Transaction, 
            Relational\ResultRow $ParentData, Relational\RelationshipChange $RelationshipChange) {
        if($RelationshipChange->HasPersistedRelationship()) {
            $PersistedRelationship = $RelationshipChange->GetPersistedRelationship();
            if($PersistedRelationship->IsIdentifying()) {
                $ParentRow = $ParentData->GetRow($this->GetParentTable());
                $ChildRow = $PersistedRelationship->GetChildResultRow()->GetRow($this->GetTable());
                $this->PersistIdentifyingRelationship($Transaction, $ParentRow, $ChildRow);
            }
            else {
                $RelatedPrimaryKey = $PersistedRelationship->GetRelatedPrimaryKey();
                if($this->IsInversed()) {
                    $this->GetForeignKey()->MapParentToReferencedKey($RelatedPrimaryKey, $ParentData);
                }
                else {
                    $this->GetForeignKey()->MapReferencedToParentKey($RelatedPrimaryKey, $ParentData);
                }
            }
        }
    }
    
    public function MapRelationalParentDataToRelatedData(
            Relational\ColumnData $ParentRow, Relational\ColumnData $RelatedRow) {
        $ForeignKey = $this->GetForeignKey();
        if($this->IsInversed()) {
            $ForeignKey->MapReferencedToParentKey($ParentRow, $RelatedRow);
        }
        else {
            $ForeignKey->MapParentToReferencedKey($ParentRow, $RelatedRow);
        }
    }
    
    final protected function PersistIdentifyingRelationship(
            Relational\Transaction $Transaction, 
            Relational\ResultRow $ParentData, Relational\ResultRow $ChildData) {
        $ForeignKey = $this->GetForeignKey();
        
        /**
         * In case the foreign key is part of the parent primary key and the row has
         * not been persisted yet, defer mapping to before persistence
         */
        $MapForeignKey = function () use (&$ParentData, &$ChildData) {
            $this->MapRelationalParentDataToRelatedData($ParentData, $ChildData);
        };
        
        $HasForeignKey = $this->IsInversed() ?
            $ForeignKey->HasReferencedKey($ParentData) :
            $ForeignKey->HasParentKey($ParentData);
        
        if($HasForeignKey) {
            $MapForeignKey();
        }
        else {
            $Transaction->SubscribeToPrePersistEvent(
                    $this->GetTable(), 
                    $MapForeignKey);
        }
    }
    
}

?>