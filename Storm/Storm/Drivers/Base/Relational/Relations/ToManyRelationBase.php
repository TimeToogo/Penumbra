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
    
    final protected function GroupRelatedRows(array $RelatedRows, array $GroupByColumns) {
        $GroupedRelatedRows = array();
        foreach($RelatedRows as $RelatedRow) {
            $Hash = $RelatedRow->GetDataFromColumns($GroupByColumns)->Hash();
            if(!isset($GroupedRelatedRows[$Hash])) {
                $GroupedRelatedRows[$Hash] = array();
            }
            $GroupedRelatedRows[$Hash][] = $RelatedRow;
        }
        
        return $GroupedRelatedRows;
    }
    
    final protected function MapParentRowsToGroupedRelatedRows(Map $Map, array $ParentRows, array $MapByColumns, array $GroupedRelatedRows) {
        foreach($ParentRows as $ParentRow) {
            $Hash = $ParentRow->GetDataFromColumns($MapByColumns)->Hash();
            if(isset($GroupedRelatedRows[$Hash])) {
                $Map->Map($ParentRow, new \ArrayObject($GroupedRelatedRows[$Hash]));
            }
            else {
                $Map->Map($ParentRow, new \ArrayObject(array()));
            }
        }
    }
    
    public function Persist(Relational\Transaction $Transaction, Relational\ColumnData $ParentData, array $RelationshipChanges) {
        
    }
}

?>