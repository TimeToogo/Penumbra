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
    
    protected function PersistIdentifyingRelationship(Relational\Transaction $Transaction, 
            Relational\Row $ParentRow, array $ChildRows) {
        $ForeignKey = $this->GetForeignKey();
        
        if($ParentRow->HasPrimaryKey()) {
            foreach($ChildRows as $ChildRow) {
                $ForeignKey->MapReferencedToParentKey($ParentRow, $ChildRow);
            }
        }
        else {
            $Transaction->SubscribeToPrePersistEvent(
                    $this->GetTable(), 
                    function () use (&$ForeignKey, &$ParentRow, &$ChildRows) {
                        foreach($ChildRows as $ChildRow) {
                            $ForeignKey->MapReferencedToParentKey($ParentRow, $ChildRow);
                        }
                    });
        }
    }
}

?>