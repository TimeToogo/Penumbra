<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;

class ToManyRelation extends ToManyRelationBase {
    public function __construct(ForeignKey $ForeignKey, Relational\Table $RelatedTable) {
        parent::__construct($ForeignKey, $RelatedTable,
                Relational\DependencyOrder::Before, Relational\DependencyOrder::Before);
    }
    
    protected function ParentTable(ForeignKey $ForeignKey) {
        return $ForeignKey->GetReferencedTable();
    }

    protected function RelatedColumns(ForeignKey $ForeignKey) {
        return $ForeignKey->GetParentColumns();
    }
    
    protected function FillParentToRelatedRowsMap(Map $Map, ForeignKey $ForeignKey, array $ParentRows, array $RelatedRows) {
        $ReferencedColumns = $ForeignKey->GetReferencedColumns();
        $ParentColumns = $ForeignKey->GetParentColumns();
        
        $GroupedRelatedRows = $this->GroupRowsByColumns($RelatedRows, $ParentColumns);
        $this->MapParentRowsToGroupedRelatedRows($Map, $ParentRows, $ReferencedColumns, $GroupedRelatedRows);
    }

    protected function MapParentRowToRelatedKey(ForeignKey $ForeignKey, Relational\ResultRow $ParentRow) {
        $ParentKey = $ParentRow->GetDataFromColumns($ForeignKey->GetReferencedColumns());
        $ReferencedKey = new Relational\ResultRow($ForeignKey->GetParentColumns());
        $ForeignKey->MapReferencedToParentKey($ParentKey, $ReferencedKey);
        
        return $ReferencedKey;
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
            $Transaction->SubscribeToPostPersistEvent($ParentRow, 
                    function (Relational\Row $ParentRow) use (&$ForeignKey, &$ChildRows) {
                        foreach($ChildRows as $ChildRow) {
                            $ForeignKey->MapReferencedToParentKey($ParentRow, $ChildRow);
                        }
                    });
        }
    }
}

?>