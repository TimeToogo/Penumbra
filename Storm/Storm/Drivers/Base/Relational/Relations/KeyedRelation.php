<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Constraints;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;

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
        $Request->AddPredicate($this->ForeignKey->GetConstraintPredicate());
    }
    
    public function AddParentPredicateToRequest(Relational\Request $Request, array $ParentRows) {
        $ParentTable = $this->GetParentTable();
        if($ParentTable) {
            $Request->AddTable($ParentTable);
        }
        $Request->AddColumns($this->GetReferencedColumns());
        $Predicate = new Constraints\Predicate();
        $RuleGroup = Constraints\RuleGroup::Any();
        
        foreach($ParentRows as $ParentRow) {
            $ReferencedKey = $this->MapParentRowToRelatedKey($this->ForeignKey, $ParentRow);
            
            $RuleGroup->AddRuleGroup(
                    Constraints\RuleGroup::Matches($ReferencedKey));
        }
        $Predicate->AddRules($RuleGroup);
        $Request->AddPredicate($Predicate);
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