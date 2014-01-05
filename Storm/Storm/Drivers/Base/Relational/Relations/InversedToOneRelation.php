<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;

class InversedToOneRelation extends ToOneRelationBase {    
    public function __construct(ForeignKey $ForeignKey, Relational\Table $RelatedTable) {
        parent::__construct($ForeignKey, $RelatedTable, 
                Relational\DependencyOrder::Before, Relational\DependencyOrder::After);
    }
    
    protected function ParentTable(ForeignKey $ForeignKey) {
        return $ForeignKey->GetReferencedTable();
    }

    protected function RelatedColumns(ForeignKey $ForeignKey) {
        return $ForeignKey->GetParentColumns();
    }
    
    protected function FillParentToRelatedRowMap(Map $Map, ForeignKey $ForeignKey, array $ParentRows, array $RelatedRows) {
        $KeyedRelatedRows = $this->HashRowsByColumnValues($RelatedRows, $ForeignKey->GetParentColumns());
        foreach($ParentRows as $ParentRow) {
            $Hash = $ParentRow->GetDataFromColumns($ForeignKey->GetReferencedColumns())->HashData();
            if(isset($KeyedRelatedRows[$Hash])) {
                $Map->Map($ParentRow, $KeyedRelatedRows[$Hash]);
            }
        }
    }

    protected function MapParentRowToRelatedKey(ForeignKey $ForeignKey, Relational\ResultRow $ParentRow) {
        $ReferencedKey = $ParentRow->GetDataFromColumns($ForeignKey->GetReferencedColumns());
        $ParentKey = new Relational\ResultRow($ForeignKey->GetParentColumns());
        $ForeignKey->MapReferencedToParentKey($ReferencedKey, $ParentKey);
        
        return $ParentKey;
    }

    
    protected function PersistIdentifyingRelationship(Relational\Transaction $Transaction, 
            Relational\Row $ParentRow, Relational\Row $ChildRow) {
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
}

?>