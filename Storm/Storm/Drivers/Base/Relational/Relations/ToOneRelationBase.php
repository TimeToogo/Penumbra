<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;

abstract class ToOneRelationBase extends KeyedRelation implements Relational\IToOneRelation {    
    final protected function NewRelationRequest() {
        return new Relational\Request(array(), true);
    }    
    
    final public function MapParentToRelatedRow(array $ParentRows, array $RelatedRows) {
        if(count($RelatedRows) > count($ParentRows)) {
            throw new \Exception;//TODO: error message
        }
        
        $Map = new Map();
        if(count($ParentRows) === 1 && count($RelatedRows) === 1) {
            $Map->Map(reset($ParentRows), reset($RelatedRows));
        } 
        else {
            $this->FillParentToRelatedRowMap($Map, $this->GetForeignKey(), $ParentRows, $RelatedRows);
        }
        
        return $Map;
    }
    protected abstract function FillParentToRelatedRowMap(Map $Map, ForeignKey $ForeignKey, array $ParentRows, array $RelatedRows);

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
    protected abstract function PersistIdentifyingRelationship(Relational\Transaction $Transaction, Relational\Row $ParentRow, Relational\Row $ChildRow);
    
}

?>