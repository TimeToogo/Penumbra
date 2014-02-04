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
        $ForeignKey = $this->GetForeignKey();
        $ForeignKey->MapReferencedToParentKey($ParentRow, $RelatedRow);
    }
    
    protected function PersistIdentifyingRelationship(Relational\Transaction $Transaction, 
            Relational\ResultRow $ParentData, array $ChildRows) {
        if($this->GetForeignKey()->HasReferencedKey($ParentData)) {
            foreach($ChildRows as $ChildRow) {
                $this->MapRelationalParentDataToRelatedData($ParentData, $ChildRow);
            }
        }
        else {
            $Transaction->SubscribeToPrePersistEvent(
                    $this->GetTable(), 
                    function () use (&$ParentData, &$ChildRows) {
                        foreach($ChildRows as $ChildRow) {
                            $this->MapRelationalParentDataToRelatedData($ParentData, $ChildRow);
                        }
                    });
        }
    }
}

?>