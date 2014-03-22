<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;

abstract class ToManyRelationBase extends KeyedRelation implements Relational\IToManyRelation {
        
    protected function JoinType() {
        return Relational\JoinType::Left;
    }
    
    final public function MapParentKeysToRelatedRows(array $ParentRows, array $RelatedRows) {
        $MappedRelatedRows = [];
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
        $GroupedRelatedRows = [];
        $GroupByKeys = Relational\ResultRow::GetAllDataFromColumns($ResultRows, $Columns);
        foreach($ResultRows as $Key => $ResultRow) {
            $Hash = $GroupByKeys[$Key]->HashData();
            if(!isset($GroupedRelatedRows[$Hash])) {
                $GroupedRelatedRows[$Hash] = [];
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
                $MappedRelatedRows[$ParentKey] = [];
            }
        }
    }
}

?>