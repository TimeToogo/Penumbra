<?php

namespace Penumbra\Drivers\Base\Relational\Relations;

use \Penumbra\Core\Relational;

abstract class Relation implements Relational\IRelation {
    use \Penumbra\Core\Helpers\Type;
    
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
    final public function GetRelatedTable() {
        return $this->Table;
    }
    
    final public function AddRelationToResultSet(Relational\ResultSetSpecification $ResultSetSpecification, array $ParentRows = null) {
        $ResultSetSpecification->GetSources()->AddJoins($this->GetRelationJoins($this->Table));
        $this->AddParentPredicate($ResultSetSpecification->GetCriteria(), $ParentRows);
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
    
    final public function GetPersistingDependencyOrder() {
        return $this->PersistingOrder;
    }
    
    final public function GetDiscardingDependencyOrder() {
        return $this->DiscardingOrder;
    }
    
    final public function RelationResultSetSelect(array $ParentRows = null) {
        $Request = $this->NewRelationSelect($ParentRows === null ? 0 : count($ParentRows));
        $this->AddParentPredicate($Request->GetCriteria(), $ParentRows);
        
        return $Request;
    }
    /**
     * @return Relational\Select
     */
    protected function NewRelationSelect($AmountOfParentRows) {
        return new Relational\ResultSetSelect(
                new Relational\ResultSetSpecification(
                        new Relational\ResultSetSources($this->Table), 
                        new Relational\Criteria()));
    }
}

?>