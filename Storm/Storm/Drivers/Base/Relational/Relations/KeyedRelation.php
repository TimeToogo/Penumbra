<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Constraints;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;

abstract class KeyedRelation extends Relation {
    private $ForeignKey;
    
    public function __construct(
            ForeignKey $ForeignKey, 
            Relational\Table $Table, 
            $PersistingOrder, 
            $DiscardingOrder) {
        parent::__construct($Table, 
                $PersistingOrder, $DiscardingOrder);
        
        $this->ForeignKey = $ForeignKey;
    }
    
    /**
     * @return ForeignKey
     */
    final public function GetForeignKey() {
        return $this->ForeignKey;
    }
    
    public function AddConstraintPredicateToRequest(Relational\Request $Request) {
        $Request->AddTable($this->GetTable());
        $Request->AddTable($this->ForeignKey->GetReferencedTable());
        $Request->AddPredicate($this->ForeignKey->GetConstraintPredicate());
    }
    
    public function AddParentPredicateToRequest(Relational\Request $Request, array $ParentRows = null) {
        $Predicate = new Constraints\Predicate();
        $RuleGroup = Constraints\RuleGroup::Any();
        $ParentColumns = $this->ForeignKey->GetParentColumns();
        $ReferencedColumns = $this->ForeignKey->GetReferencedColumns();
        foreach($ParentRows as $ParentRow) {
            $ReferencedKey = $this->MapParentRowToReferencedKey($this->ForeignKey, $ParentRow);
            
            $RuleGroup->AddRuleGroup(
                    Constraints\RuleGroup::Matches($ReferencedKey));
        }
        $Predicate->AddRules($RuleGroup);
        $Request->AddPredicate($Predicate);
    }
    /**
     * @return Relational\ColumnData
     */
    protected abstract function MapParentRowToReferencedKey(ForeignKey $ForeinKey, 
            Relational\ResultRow $ParentRow);
    
    final protected function MapParentToRelatedRows(Map $Map, array $ParentRows, array $RelatedRows) {
        return $this->MapParentToRelatedRowsByKey($Map, $ParentRows, $RelatedRows, 
                $this->ForeignKey->GetParentColumns(), $this->ForeignKey->GetReferencedColumns());
    }
    protected abstract function MapParentToRelatedRowsByKey(
            Map $Map, array $ParentRows, array $RelatedRows, 
            array $ParentColumns, array $ReferencedColumns);
}

?>