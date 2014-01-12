<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Constraints;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;
use \Storm\Drivers\Base\Relational\Expressions;

abstract class KeyedRelation extends Relation {
    private $ForeignKey;
    private $IsInversed;
    
    public function __construct(
            ForeignKey $ForeignKey,
            Relational\Table $RelatedTable, 
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

    public function AddConstraintToRequest(Relational\Request $Request) {
        $Request->GetCriterion()->AddPredicateExpression($this->ForeignKey->GetConstraintPredicate());
    }
    
    public function AddParentPredicateToRequest(Relational\Request $Request, array $ParentRows) {
        $ParentTable = $this->GetParentTable();
        if($ParentTable) {
            $Request->AddTable($ParentTable);
        }
        $Request->AddColumns($this->GetReferencedColumns());
        
        $MatchExpressions = array();
        foreach($ParentRows as $ParentRow) {
            $ReferencedKey = $this->MapParentRowToRelatedKey($this->ForeignKey, $ParentRow);
            
            $MatchExpressions[] = new Expressions\MatchesColumnDataExpression($ReferencedKey);
        }
        
        $Request->GetCriterion()->AddPredicateExpression(
                new Expressions\PredicateExpression($MatchExpressions, Expressions\Operators\Binary::LogicalOr));
    }
    /**
     * @return Relational\Table
     */
    protected function GetParentTable() {
        return $this->IsInversed ? 
                $this->ForeignKey->GetReferencedTable() : $this->ForeignKey->GetParentTable();
    }
    /**
     * @return Relational\IColumn[]
     */
    protected function GetReferencedColumns() {
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
            $ReferencedKey = $ParentRow->GetDataFromColumns($ForeignKey->GetReferencedColumns());
            $ParentKey = new Relational\ResultRow($ForeignKey->GetParentColumns());
            $ForeignKey->MapReferencedToParentKey($ReferencedKey, $ParentKey);

            return $ParentKey;
        }
        else {
            $ParentKey = $ParentRow->GetDataFromColumns($ForeignKey->GetParentColumns());
            $ReferencedKey = new Relational\ResultRow($ForeignKey->GetReferencedColumns());
            $ForeignKey->MapParentToReferencedKey($ParentKey, $ReferencedKey);
            
            return $ReferencedKey;
        }
    }
    
    final protected function HashRowsByColumnValues(array $ResultRows, array $Columns) {
        $KeyedRows = array();
        $ColumnDataArray = Relational\ResultRow::GetAllDataFromColumns($ResultRows, $Columns);
        foreach($ResultRows as $Key => $Row) {
            $Hash = $ColumnDataArray[$Key]->HashData();
            $KeyedRows[$Hash] = $Row;
        }
        
        return $KeyedRows;
    }
}

?>