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
        }
    }
    
    final protected function PersistIdentifyingRelationship(
            Relational\Transaction $Transaction, 
            Relational\Row $ParentRow, Relational\Row $ChildRow) {
        
        if($this->IsInversed()) {
            if($ParentRow->HasPrimaryKey()) {
                $this->GetForeignKey()->MapReferencedToParentKey($ParentRow, $ChildRow);
            }
            else {
                $Transaction->SubscribeToPrePersistEvent($ChildRow, 
                        function () use (&$ParentRow, &$ChildRow) {
                            $this->GetForeignKey()->MapReferencedToParentKey($ParentRow, $ChildRow);
                        });
            }
        }
        else {
            if($ParentRow->HasPrimaryKey()) {
                $this->GetForeignKey()->MapParentToReferencedKey($ParentRow, $ChildRow);
            }
            else {
                $Transaction->SubscribeToPrePersistEvent($ChildRow, 
                        function () use (&$ParentRow, &$ChildRow) {
                            $this->GetForeignKey()->MapParentToReferencedKey($ParentRow, $ChildRow);
                        });
            }
        }
    }
    
}

?>