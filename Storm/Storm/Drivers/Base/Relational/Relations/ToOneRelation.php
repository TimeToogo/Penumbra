<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;

class ToOneRelation extends ToOneRelationBase {
    public function __construct(ForeignKey $ForeignKey) {
         parent::__construct($ForeignKey, 
                $ForeignKey->GetReferencedTable(), 
                Relational\DependencyOrder::After, 
                Relational\DependencyOrder::Before);
    }
    
    protected function ParentTable(ForeignKey $ForeignKey) {
        return $ForeignKey->GetParentTable();
    }

    protected function RelatedColumns(ForeignKey $ForeignKey) {
        return $ForeignKey->GetReferencedColumns();
    }
    
    protected function FillParentToRelatedRowMap(Map $Map, ForeignKey $ForeignKey, array $ParentRows, array $RelatedRows) {
        $KeyedRelatedRows = $this->HashRowsByColumnValues($RelatedRows, $ForeignKey->GetReferencedColumns());
        foreach($ParentRows as $ParentRow) {
            $Hash = $ParentRow->GetDataFromColumns($ForeignKey->GetParentColumns())->Hash(false);
            if(isset($KeyedRelatedRows[$Hash])) {
                $Map->Map($ParentRow, $KeyedRelatedRows[$Hash]);
            }
        }
    }

    protected function MapParentRowToRelatedKey(ForeignKey $ForeignKey, Relational\ResultRow $ParentRow) {
        $ParentKey = $ParentRow->GetDataFromColumns($ForeignKey->GetParentColumns());
        $ReferencedKey = new Relational\ResultRow($ForeignKey->GetReferencedColumns());
        $ForeignKey->MapParentToReferencedKey($ParentKey, $ReferencedKey);
        
        return $ReferencedKey;
    }
    
    protected function PersistIdentifyingRelationship(Relational\Transaction $Transaction, 
            Relational\Row $ParentRow, Relational\Row $ChildRow) {
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

?>