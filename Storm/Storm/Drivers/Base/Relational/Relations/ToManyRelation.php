<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;

class ToManyRelation extends ToManyRelationBase {
    public function __construct(ForeignKey $ForeignKey) {
        parent::__construct($ForeignKey, 
                $ForeignKey->GetParentTable(),
                Relational\DependencyOrder::Before, 
                Relational\DependencyOrder::Before);
    }
    
    protected function GroupRelatedRowsByParentKeys(array &$MappedRelatedRows, ForeignKey $ForeignKey, array $ParentRows, array $RelatedRows) {
        $ReferencedColumns = $ForeignKey->GetReferencedColumns();
        $ParentColumns = $ForeignKey->GetParentColumns();
        
        $GroupedRelatedRows = $this->GroupRowsByColumnValues($RelatedRows, $ParentColumns);
        $this->MapParentRowKeysToGroupedRelatedRows($MappedRelatedRows, $ParentRows, $ReferencedColumns, $GroupedRelatedRows);
    }
    
    public function MapRelationalParentDataToRelatedData
            (Relational\ColumnData $ParentRow, Relational\ColumnData $RelatedRow) {
        $this->ForeignKey->MapReferencedToParentKey($ParentRow, $RelatedRow);
    }
    
    public function Persist(
            Relational\Transaction $Transaction, 
            Relational\ResultRow $ParentData, 
            array $DiscardedPrimaryKeys, 
            array $PersistedRelatedDataArray) {
        
        $MapForeignKeys = function () use (&$ParentData, &$DiscardedPrimaryKeys, &$PersistedRelatedDataArray) {
            foreach(array_merge($DiscardedPrimaryKeys, $PersistedRelatedDataArray) as $RelatedData) {
                if($RelatedData !== null) {
                    $this->ForeignKey->MapReferencedToParentKey($ParentData, $RelatedData);
                }
            }
        };
        
        $ParentHasForeignKey = $this->ForeignKey->HasReferencedKey($ParentData);
        
        /**
         * In case the foreign key is part of the parent primary key and the row has
         * not been persisted yet, defer mapping to before persistence
         */
        if($ParentHasForeignKey) {
            $MapForeignKeys();
        }
        else {
            $Transaction->SubscribeToPrePersistEvent(
                    $this->GetTable(), 
                    $MapForeignKeys);
        }
    }
}

?>