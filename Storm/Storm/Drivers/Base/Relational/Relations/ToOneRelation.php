<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Constraints;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;

class ToOneRelation extends ToOneKeyedRelation {
    public function __construct(ForeignKey $ForeignKey) {
        parent::__construct($ForeignKey, $ForeignKey->GetReferencedTable(), 
                Relational\DependencyOrder::After, Relational\DependencyOrder::Before);
    }
    
    protected function MapParentRowToReferencedKey(ForeignKey $ForeinKey, 
            Relational\ResultRow $ParentRow) {
        $ParentKey = $ParentRow->GetDataFromColumns($ForeinKey->GetParentColumns());
        $ReferencedKey = $ParentRow->GetDataFromColumns($ForeinKey->GetReferencedColumns());
        $ForeinKey->MapParentKey($ParentKey, $ReferencedKey);
        
        return $ReferencedKey;
    }
    
    public function MapParentToRelatedRowsByKey(
            Map $Map, array $ParentRows, array $RelatedRows, 
            array $ParentColumns, array $ReferencedColumns) {
        
        $KeyedParentRows = $this->HashRowsByColumns($ParentRows, $ParentColumns);
        $KeyedRelatedRows = $this->HashRowsByColumns($RelatedRows, $ReferencedColumns);
        foreach($KeyedParentRows as $Hash => $KeyedParentRow) {
            if(isset($KeyedRelatedRows[$Hash])) {
                $Map->Map($KeyedParentRow, $KeyedRelatedRows[$Hash]);
            }
        }
    }
    
    public function Persist(Relational\Transaction $Transaction, Relational\ColumnData $ParentData, Relational\Row $RelatedRow) {
        
    }

    public function Discard(Relational\Transaction $Transaction, 
            Relational\PrimaryKey $PrimaryKey) {
        $RelatedPrimaryKey = $this->GetTable()->Row();
        $this->ForeignKey->MapReferencedKey($PrimaryKey, $RelatedPrimaryKey);
        
        $Table = $RelatedPrimaryKey->GetTable();
        
        $Request = new Relational\Request($Table, true);
        $Request->AddPredicate(Predicate::On($Table)
                        ->Matches($RelatedPrimaryKey));
        
        $Transaction->DiscardAll($Request);
    }
}

?>