<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Relational;

abstract class Relation implements Relational\IRelation {
    use \Storm\Core\Helpers\Type;
    
    private $Table;
    private $PersistingOrder;
    private $DiscardingOrder;
    
    public function __construct(Relational\ITable $RelatedTable, $PersistingOrder, $DiscardingOrder) {
        $this->Table = $RelatedTable;
        $this->PersistingOrder = $PersistingOrder;
        $this->DiscardingOrder = $DiscardingOrder;
    }
    
    /**
     * @return Relational\ITable
     */
    final public function GetTable() {
        return $this->Table;
    }
    
    final public function AddRelationToSelect(Relational\Select $Request, array $ParentRows = null) {
        $this->AddRelationToCriterion($Request->GetCriterion(), $ParentRows);
        
        if($ParentRows !== null && count($ParentRows) > 0) {
            $this->AddParentColumnsToRequest($Request);
        }
    }
    
    final public function AddRelationToCriterion(Relational\Criterion $Criterion, array $ParentRows = null) {
        $Criterion->AddJoins($this->RelationJoins());
        $this->AddParentPredicate($Criterion, $ParentRows);
    }
    
    final protected function RelationJoins() {
        return $this->GetRelationJoins($this->Table);
    }
    
    /**
     * Relational\Join[]
     */
    protected abstract function GetRelationJoins(Relational\ITable $Table);
    private function AddParentPredicate(Relational\Criterion $Criterion, array $ParentRows = null) {
        if($ParentRows !== null && count($ParentRows) > 0) {
            $this->AddParentPredicateToCriterion($Criterion, $ParentRows);
        }
    }
    protected abstract function AddParentPredicateToCriterion(Relational\Criterion $Criterion, array $ParentRows);
    
    protected abstract function AddParentColumnsToRequest(Relational\Select $Request);
    
    final public function GetPersistingDependencyOrder() {
        return $this->PersistingOrder;
    }
    
    final public function GetDiscardingDependencyOrder() {
        return $this->DiscardingOrder;
    }
    
    final public function RelationSelect(array $ParentRows = null) {
        $Request = $this->NewRelationRequest();
        $this->AddParentPredicate($Request->GetCriterion(), $ParentRows);
        
        return $Request;
    }
    /**
     * Relational\Request
     */
    protected function NewRelationRequest() {
        return new Relational\Select(new Relational\Criterion($this->Table));
    }
}

?>