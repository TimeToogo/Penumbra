<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Constraints\Predicate;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;

class JoinTableRelation extends Relation implements Relational\IToManyRelation {
    private $JoinTable;
    private $ForeignKey;
    private $OtherForeignKey;
    private $IsTableTwo;
    
    public function __construct(JoinTable $JoinTable, $IsTableTwo = true) {
        parent::__construct($IsTableTwo ? $JoinTable->GetTable2() : $JoinTable->GetTable1(), 
                Relational\DependencyOrder::After, Relational\DependencyOrder::Before);
        
        $this->JoinTable = $JoinTable;
        $this->IsTableTwo = $IsTableTwo;
        
        $this->ForeignKey = $this->IsTableTwo ? 
                $this->JoinTable->GetForeignKey2() : $this->JoinTable->GetForeignKey1();
        
        $this->OtherForeignKey = !$this->IsTableTwo ? 
                $this->JoinTable->GetForeignKey2() : $this->JoinTable->GetForeignKey1();
        
    }

    /**
     * @return JoinTable
     */
    final public function GetJoinTable() {
        return $this->JoinTable;
    }
    
    /**
     * @return ForeignKey
     */
    final public function GetForeignKey() {
        return $this->ForeignKey;
    }
    
    /**
     * @return ForeignKey
     */
    final public function GetOtherForeignKey() {
        return $this->OtherForeignKey;
    }
    
    public function AddConstraintPredicateToRequest(Relational\Request $Request) {
        $Request->AddTable($this->JoinTable);
        $Request->AddTable($this->JoinTable->GetTable1());
        $Request->AddTable($this->JoinTable->GetTable2());
        $Request->AddPredicate($this->JoinTable->GetForeignKey1()->GetConstraintPredicate());
        $Request->AddPredicate($this->JoinTable->GetForeignKey2()->GetConstraintPredicate());
    }
    
    public function AddParentPredicateToRequest(Relational\Request $Request, array $ParentRows) {
        $Request->AddTable($this->JoinTable);
        $Request->AddColumns($this->OtherForeignKey->GetParentColumns());
        
        $Predicate = new Constraints\Predicate();
        $RuleGroup = Constraints\RuleGroup::Any();
        $JoinTableColumns = $this->OtherForeignKey->GetParentColumns();
        $ReferencedColumns = $this->OtherForeignKey->GetReferencedColumns();
        foreach($ParentRows as $ParentRow) {
            $ParentKey = $ParentRow->GetDataFromColumns($ReferencedColumns);
            $JoinTableKey = new Relational\ResultRow($JoinTableColumns);
            $this->OtherForeignKey->MapReferencedKey($ParentKey, $JoinTableKey);

            $RuleGroup->AddRuleGroup(
                    Constraints\RuleGroup::Matches($JoinTableKey));
        }
        $Predicate->AddRules($RuleGroup);
        $Request->AddPredicate($Predicate);
    }
    
    protected function MapParentToRelatedRows(Map $Map, array $ParentRows, array $RelatedRows) {
        $GroupedRelatedRows = array();
        $OtherJoinTableKeys = $this->OtherForeignKey->GetParentColumns();
        $ParentKeys = $this->OtherForeignKey->GetReferencedColumns();
        
        foreach($RelatedRows as $RelatedRow) {
            $Hash = $RelatedRow->GetDataFromColumns($OtherJoinTableKeys);
            if(!isset($GroupedRelatedRows[$Hash])) {
                $GroupedRelatedRows[$Hash] = array();
            }
            $GroupedRelatedRows[$Hash][] = $RelatedRow;
        }
        
        foreach($ParentRows as $ParentRow) {
            $Hash = $ParentRow->GetDataFromColumns($ParentKeys);
            if(!isset($GroupedRelatedRows[$Hash])) {
                $Map->Map($ParentRow, new \ArrayObject(array()));
            }
            else {
                $Map->Map($ParentRow, new \ArrayObject($GroupedRelatedRows[$Hash]));
            }
        }
    }

    public function Persist(Relational\Transaction $Transaction, Relational\Row $Row, 
            array $PersistedRelatedRows, array $DiscardedPrimaryKeys) {
        
        $PrimaryKey = $Row->GetPrimaryKey();
        foreach($PersistedRelatedRows as $RelatedRow) {
            $Transaction->Persist($RelatedRow);
            
            $RelatedPrimaryKey = $RelatedRow->GetPrimaryKey();
            $JoinRow = $this->JoinTable->JoinRow($PrimaryKey, $RelatedPrimaryKey);
            $Transaction->Persist($JoinRow);
        }
        foreach($DiscardedPrimaryKeys as $PrimaryKey) {
            $Request = $this->MapJoinedPrimaryKey($PrimaryKey);
            $Transaction->DiscardAll($Request);
        }
    }
    public function Discard(Relational\Transaction $Transaction, Relational\PrimaryKey $PrimaryKey) {
        $Request = $this->MapJoinedPrimaryKey($PrimaryKey);
        $Transaction->DiscardAll($Request);
    }
    
    private function MapJoinedPrimaryKey(Relational\PrimaryKey $PrimaryKey) {
        $ForeignKey = $this->JoinTable->GetForeignKey($PrimaryKey->GetTable());
        $ForeignKeyData = $this->JoinTable->Row();
        $ForeignKey->MapParentKey($PrimaryKey, $ForeignKeyData);

        $Request = new Relational\Request($this->JoinTable, false);
        $Predicate = Predicate::On($this->JoinTable)
                ->Matches($ForeignKeyData);
        $Request->AddPredicate($Predicate);
        
        return $Request;
    }
}

?>