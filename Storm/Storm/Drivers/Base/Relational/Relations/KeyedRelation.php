<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Constraints;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;
use \Storm\Drivers\Base\Relational\Expressions as R;

abstract class KeyedRelation extends Relation {
    /**
     * @var ForeignKey 
     */
    protected $ForeignKey;
    protected $IsInversed;
    
    public function __construct(
            ForeignKey $ForeignKey,
            Relational\ITable $RelatedTable, 
            $PersistingOrder, 
            $DiscardingOrder) {
        parent::__construct($RelatedTable, 
                $PersistingOrder, $DiscardingOrder);
        
        $this->ForeignKey = $ForeignKey;
        $this->IsInversed = $ForeignKey->GetParentTable()->Is($RelatedTable);
    }
    
    /**
     * @return ForeignKey
     */
    final public function GetForeignKey() {
        return $this->ForeignKey;
    }
    
    final public function IsInversed() {
        return $this->IsInversed;
    }
    
    protected function GetRelationJoins(Relational\ITable $Table) {
        return [new Relational\Join($this->JoinType(), $Table, $this->ForeignKey->GetConstraintPredicate())];
    }
    
    protected abstract function JoinType();
    
    protected function AddParentPredicateToCriteria(Relational\Criteria $Criteria, array $ParentRows) {
        $MatchExpressions = [];
        foreach($ParentRows as $ParentRow) {
            $ReferencedKey = $this->MapParentRowToRelatedKey($this->ForeignKey, $ParentRow);
            $Hash = $ReferencedKey->HashData();
            if(!isset($MatchExpressions[$Hash])) {
                $MatchExpressions[$Hash] = new R\MatchesColumnDataExpression($ReferencedKey);
            }
        }
        
        $Criteria->AddPredicateExpression(
                R\Expression::CompoundBoolean($MatchExpressions, R\Operators\Binary::LogicalOr));
    }
    /**
     * @return Relational\Table
     */
    public function GetParentTable() {
        return $this->IsInversed ? 
                $this->ForeignKey->GetReferencedTable() : $this->ForeignKey->GetParentTable();
    }
    
    /**
     * @return Relational\IColumn[]
     */
    public function GetParentColumns() {
        return $this->IsInversed ? 
                $this->ForeignKey->GetReferencedColumns() : $this->ForeignKey->GetParentColumns();
    }
    
    /**
     * @return Relational\IColumn[]
     */
    public function GetReferencedColumns() {
        return $this->IsInversed ? 
                $this->ForeignKey->GetParentColumns() : $this->ForeignKey->GetReferencedColumns();
    }
    
    /**
     * @return Relational\ResultRow
     */
    protected function MapParentRowToRelatedKey(
            ForeignKey $ForeignKey, 
            Relational\ResultRow $ParentRow) {
        if($this->IsInversed) {
            $ParentKey = $ForeignKey->ParentKey();
            $ForeignKey->MapReferencedToParentKey($ParentRow, $ParentKey);

            return $ParentKey;
        }
        else {
            $ReferencedKey = $ForeignKey->ReferencedKey();
            $ForeignKey->MapParentToReferencedKey($ParentRow, $ReferencedKey);
            
            return $ReferencedKey;
        }
    }
    
    final protected function MakeHashedDataToKeyMap(array $ResultRows, array $Columns) {
        return array_combine(
                array_keys($this->IndexRowsByHashedColumnValues($ResultRows, $Columns)), 
                array_keys($ResultRows));
    }

    final protected function IndexRowsByHashedColumnValues(array $ResultRows, array $Columns) {
        $KeyedRows = [];
        $ColumnDataArray = Relational\ResultRow::GetAllDataFromColumns($ResultRows, $Columns);
        foreach($ResultRows as $Key => $Row) {
            $Hash = $ColumnDataArray[$Key]->HashData();
            $KeyedRows[$Hash] = $Row;
        }
        
        return $KeyedRows;
    }
}

?>