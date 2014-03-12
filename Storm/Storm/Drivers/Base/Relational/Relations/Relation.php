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
        $this->AddRelationToCriteria($Request->GetCriteria(), $ParentRows);
        
        if($ParentRows !== null && count($ParentRows) > 0) {
            $this->AddParentColumnsToRequest($Request);
        }
    }
    
    final public function AddRelationToCriteria(Relational\Criteria $Criteria, array $ParentRows = null) {
        $Criteria->AddJoins($this->RelationJoins());
        $this->AddParentPredicate($Criteria, $ParentRows);
    }
    
    final protected function RelationJoins() {
        return $this->GetRelationJoins($this->Table);
    }
    
    /**
     * Relational\Join[]
     */
    protected abstract function GetRelationJoins(Relational\ITable $Table);
    private function AddParentPredicate(Relational\Criteria $Criteria, array $ParentRows = null) {
        if($ParentRows !== null && count($ParentRows) > 0) {
            $this->AddParentPredicateToCriteria($Criteria, $ParentRows);
        }
    }
    protected abstract function AddParentPredicateToCriteria(Relational\Criteria $Criteria, array $ParentRows);
    
    protected abstract function AddParentColumnsToRequest(Relational\Select $Request);
    
    final public function GetPersistingDependencyOrder() {
        return $this->PersistingOrder;
    }
    
    final public function GetDiscardingDependencyOrder() {
        return $this->DiscardingOrder;
    }
    
    final public function RelationSelect($Type, array $ParentRows = null) {
        $Request = $this->NewRelationRequest($Type);
        $this->AddParentPredicate($Request->GetCriteria(), $ParentRows);
        
        return $Request;
    }
    /**
     * Relational\Request
     */
    protected function NewRelationRequest($Type) {
        return new Relational\Select($Type, new Relational\Criteria($this->Table));
    }
}

?>