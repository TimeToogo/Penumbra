<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Constraints\Predicate;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;

class InversedToOneRelation extends ToOneKeyedRelation {
    public function __construct(Relational\Table $RelatedTable, ForeignKey $ForeignKey) {
        parent::__construct($ForeignKey, $RelatedTable, 
                Relational\DependencyOrder::Before, Relational\DependencyOrder::After);
    }
    
    protected function MapParentRowToReferencedKey(ForeignKey $ForeinKey, 
            Relational\ResultRow $ParentRow) {
        $ParentKey = $ParentRow->GetDataFromColumns($ForeinKey->GetReferencedColumns());
        $ReferencedKey = $ParentRow->GetDataFromColumns($ForeinKey->GetParentColumns());
        $ForeinKey->MapReferencedKey($ParentKey, $ReferencedKey);
        
        return $ReferencedKey;
    }
    
    public function MapParentToRelatedRowsByKey(
            Map $Map, array $ParentRows, array $RelatedRows, 
            array $ParentColumns, array $ReferencedColumns) {
        
        $KeyedParentRows = $this->HashRowsByColumns($ParentRows, $ReferencedColumns);
        $KeyedRelatedRows = $this->HashRowsByColumns($RelatedRows, $ParentColumns);
        foreach($KeyedParentRows as $Hash => $KeyedParentRow) {
            if(isset($KeyedRelatedRows[$Hash])) {
                $Map->Map($KeyedParentRow, $KeyedRelatedRows[$Hash]);
            }
        }
    }
    
    /**
     * @return ForeignKey
     */
    public function GetForeignKey() {
        return $this->ForeignKey;
    }
    public function Persist(Relational\Transaction $Transaction, Relational\ColumnData $ParentData, Relational\Row $RelatedRow) {
        
    }

    public function Discard(Relational\Transaction $Transaction, Relational\PrimaryKey $PrimaryKey) {
        $RelatedPrimaryKey = $this->GetTable()->Row();
        $this->ForeignKey->MapParentKey($PrimaryKey, $RelatedPrimaryKey);
        
        $Table = $RelatedPrimaryKey->GetTable();
        
        $Request = new Relational\Request($Table);
        $Request->AddPredicate(Predicate::On($Table)
                        ->Matches($RelatedPrimaryKey));
        
        $Transaction->DiscardAll($Request);
    }
}

?>