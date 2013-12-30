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
            Relational\Table $RelatedTable, 
            $PersistingOrder, 
            $DiscardingOrder) {
        parent::__construct($RelatedTable, 
                $PersistingOrder, $DiscardingOrder);
        
        $this->ForeignKey = $ForeignKey;
    }
    
    /**
     * @return ForeignKey
     */
    final public function GetForeignKey() {
        return $this->ForeignKey;
    }
    
    public function AddConstraintToRequest(Relational\Request $Request) {
        $Request->AddPredicate($this->ForeignKey->GetConstraintPredicate());
    }
    
    public function AddParentPredicateToRequest(Relational\Request $Request, array $ParentRows) {
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
     * @return Relational\ResultRow
     */
    protected abstract function GetParentTables(ForeignKey $ForeignKey);
    /**
     * @return Relational\ResultRow
     */
    protected abstract function MapParentRowToRelatedKey(ForeignKey $ForeignKey, 
            Relational\ResultRow $ParentRow);
    
    
    final protected function HashRowsByColumns(array $Rows, array $Columns) {
        $KeyedRows = array();
        foreach($Rows as $Row) {
            $Hash = $Row->GetDataFromColumns($Columns)->Hash();
            $KeyedRows[$Hash] = $Row;
        }
        
        return $KeyedRows;
    }
}

?>