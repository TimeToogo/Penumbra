<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;

abstract class ToOneRelationBase extends KeyedRelation implements Relational\IToOneRelation {
    
    final public function MapParentToRelatedRow(array $ParentRows, array $RelatedRows) {
        if(count($RelatedRows) > count($ParentRows)) {
            throw new \Exception;//TODO: error message
        }
        
        $Map = new Map();
        if(count($ParentRows) === 1 && count($RelatedRows) === 1) {
            $Map->Map(reset($ParentRows), reset($RelatedRows));
        } 
        else {
            $ForeignKey = $this->GetForeignKey();
            if($this->IsInversed()) {
                $KeyedRelatedRows = $this->HashRowsByColumnValues($RelatedRows, $ForeignKey->GetParentColumns());
                $ParentReferencedKeys = Relational\ResultRow::GetAllDataFromColumns($ParentRows, $ForeignKey->GetReferencedColumns());
                foreach($ParentRows as $Key => $ParentRow) {
                    $Hash = $ParentReferencedKeys[$Key]->HashData();
                    if(isset($KeyedRelatedRows[$Hash])) {
                        $Map->Map($ParentRow, $KeyedRelatedRows[$Hash]);
                    }
                }
            }
            else {
                $KeyedRelatedRows = $this->HashRowsByColumnValues($RelatedRows, $ForeignKey->GetReferencedColumns());
                $ParentReferencedKeys = Relational\ResultRow::GetAllDataFromColumns($ParentRows, $ForeignKey->GetParentColumns());
                foreach($ParentRows as $ParentRow) {
                    $Hash = $ParentReferencedKeys[$Key]->HashData();
                    if(isset($KeyedRelatedRows[$Hash])) {
                        $Map->Map($ParentRow, $KeyedRelatedRows[$Hash]);
                    }
                }
            }
        }
        
        return $Map;
    }
    
    final protected function MapKeyIntersection(Map $Map, array $KeyedParentRows, array $KeyedRelatedRows) {
        foreach(array_intersect_key($KeyedParentRows, $KeyedRelatedRows) as $Key => $ParentRow) {
            $Map->Map($ParentRow, $KeyedRelatedRows[$Key]);
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
                        function (Relational\Row $ChildRow) use (&$ParentRow) {
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
                        function (Relational\Row $ChildRow) use (&$ParentRow) {
                            $this->GetForeignKey()->MapParentToReferencedKey($ParentRow, $ChildRow);
                        });
            }
        }
    }
    
}

?>