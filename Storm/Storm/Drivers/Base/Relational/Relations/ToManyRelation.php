<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Constraints\Predicate;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;

class ToManyRelation extends KeyedRelation implements Relational\IToManyRelation {
    public function __construct(Relational\Table $RelatedTable, ForeignKey $ForeignKey) {
        parent::__construct($ForeignKey, $RelatedTable,
                Relational\DependencyOrder::Before, Relational\DependencyOrder::Before);
    }
    
    protected function MapParentRowToReferencedKey(ForeignKey $ForeinKey, 
            Relational\ResultRow $ParentRow) {
        $ParentKey = $ParentRow->GetDataFromColumns($ForeinKey->GetReferencedColumns());
        $ReferencedKey = $ParentRow->GetDataFromColumns($ForeinKey->GetParentColumns());
        $ForeinKey->MapReferencedKey($ParentKey, $ReferencedKey);
        
        return $ReferencedKey;
    }
    
    protected function MapParentToRelatedRowsByKey(
            Map $Map, array $ParentRows, array $RelatedRows, 
            array $ParentColumns, array $ReferencedColumns) {
        $GroupedRelatedRows = array();
        foreach($RelatedRows as $RelatedRow) {
            $Hash = $RelatedRow->GetDataFromColumns($ParentColumns)->Hash();
            if(!isset($GroupedRelatedRows[$Hash])) {
                $GroupedRelatedRows[$Hash] = array();
            }
            $GroupedRelatedRows[$Hash][] = $RelatedRow;
        }
        
        foreach($ParentRows as $ParentRow) {
            $Hash = $ParentRow->GetDataFromColumns($ReferencedColumns)->Hash();
            if(!isset($GroupedRelatedRows[$Hash])) {
                $Map->Map($ParentRow, new \ArrayObject(array()));
            }
            else {
                $Map->Map($ParentRow, new \ArrayObject($GroupedRelatedRows[$Hash]));
            }
        }
    }
    
    public function Persist(Relational\Transaction $Transaction, Relational\ColumnData $ParentData, array $PersistedRelatedRows, array $DiscardedPrimaryKeys) {
        foreach($PersistedRelatedRows as $PersistedRelatedRow) {
            $Transaction->PersistAll($PersistedRelatedRow->GetRows());
        }
        foreach($DiscardedPrimaryKeys as $DiscardedPrimaryKey) {
            $Transaction->Discard($DiscardedPrimaryKey);
        }
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