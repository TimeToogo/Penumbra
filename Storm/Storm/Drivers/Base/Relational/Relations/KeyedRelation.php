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
            
            $MatchExpressions[] = new Expressions\MatchesColumnDataExpression($this->GetTable(), $ReferencedKey);
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
    
    final protected function MakeHashedDataToKeyMap(array $ResultRows, array $ColumnIdentifiers) {
        return array_combine(
                $this->IndexRowsByHashedColumnValues($ResultRows, $ColumnIdentifiers), 
                array_keys($ResultRows));
    }
    
    final protected function IndexRowsByHashedColumnValues(array $ResultRows, array $ColumnIdentifiers) {
        $KeyedResultRows = array();
        if(count($ColumnIdentifiers) === 1) {
            $ColumnIdentifier = reset($ColumnIdentifiers);
            foreach($ResultRows as $Key => &$ResultRow) {
                $Hash = md5(json_encode($ResultRow[$ColumnIdentifier]));
                $KeyedResultRows[$Hash] =& $ResultRow;
            }
        }
        else {
            $ColumnIdentifiers = array_flip(array_values($ColumnIdentifiers));
            foreach($ResultRows as $Key => &$ResultRow) {
                $HashValues = array_intersect_key($ResultRow, $ColumnIdentifiers);
                ksort($HashValues);
                $Hash = md5(json_encode(array_values($HashValues)));
                $KeyedResultRows[$Hash] =& $ResultRow;
            }
        }
        
        return $KeyedResultRows;
    }
    
    /**
     * @return Relational\ResultRow
     */
    protected function MapParentRowToRelatedKey(ForeignKey $ForeignKey, array $ParentRow) {
        if($this->IsInversed) {
            $ReferencedKey = $ParentRow;
            $ParentKey = array();
            $ForeignKey->MapReferencedToParentKey($ReferencedKey, $ParentKey);

            return $ParentKey;
        }
        else {
            $ParentKey = $ParentRow;
            $ReferencedKey = array();
            $ForeignKey->MapParentToReferencedKey($ParentKey, $ReferencedKey);
            
            return $ReferencedKey;
        }
    }
}

?>