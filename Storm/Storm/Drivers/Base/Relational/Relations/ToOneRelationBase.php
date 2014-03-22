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
            
            $ForeignKey = $this->ForeignKey;
            if($this->IsInversed) {
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
    
    public function Persist(
            Relational\Transaction $Transaction, 
            Relational\ResultRow $ParentData, 
            Relational\PrimaryKey $DiscardedPrimaryKey = null, 
            Relational\ColumnData $RelatedData = null) {
        
        if($RelatedData !== null) {
            
            if($this->IsInversed) {
                $MapForeignKey = function () use (&$ParentData, &$RelatedData) {
                    $this->ForeignKey->MapReferencedToParentKey($ParentData, $ParentData);
                };
                $HasForeignKey = $this->ForeignKey->HasReferencedKey($ParentData);
                $BeforeTable = $this->GetTable();
            }
            else {
                $MapForeignKey = function () use (&$ParentData, &$RelatedData) {
                    $this->ForeignKey->MapReferencedToParentKey($RelatedData, $ParentData);
                };
                $HasForeignKey = $this->ForeignKey->HasReferencedKey($RelatedData);
                $BeforeTable = $this->GetParentTable();
            }

            /**
             * In case the foreign key is part of the parent primary key and the row has
             * not been persisted yet, defer mapping to before persistence
             */
            if($HasForeignKey) {
                $MapForeignKey();
            }
            else {
                $Transaction->SubscribeToPrePersistEvent(
                        $BeforeTable, 
                        $MapForeignKey);
            }
        }
        if($DiscardedPrimaryKey !== null) {
            $this->MapRelationalParentDataToRelatedData($ParentData, $DiscardedPrimaryKey);
        }
    }  
}

?>