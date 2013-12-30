<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Relational;

abstract class Relation implements Relational\IRelation {
    use \Storm\Core\Helpers\Type;
    
    private $Table;
    private $PersistingOrder;
    private $DiscardingOrder;
    
    public function __construct(Relational\Table $RelatedTable, $PersistingOrder, $DiscardingOrder) {
        $this->Table = $RelatedTable;
        $this->PersistingOrder = $PersistingOrder;
        $this->DiscardingOrder = $DiscardingOrder;
    }
    
    /**
     * @return Relational\Table
     */
    final public function GetTable() {
        return $this->Table;
    }
    
    final public function AddRelationToRequest(Relational\Request $Request, array $ParentRows = null) {
        $Request->AddTable($this->Table);
        $this->AddConstraintToRequest($Request);
        if($ParentRows !== null && count($ParentRows) > 0) {
            $this->AddParentPredicateToRequest($Request, $ParentRows);
        }
    }
    /**
     * Relational\Request
     */
    protected abstract function AddConstraintToRequest(Relational\Request $Request);
    
    protected abstract function AddParentPredicateToRequest(Relational\Request $Request, array $ParentRows);
    
    final public function GetPersistingDependencyOrder() {
        return $this->PersistingOrder;
    }
    
    final public function GetDiscardingDependencyOrder() {
        return $this->DiscardingOrder;
    }
    
    final public function RelationRequest(array $ParentRows = null) {
        $Request = $this->NewRelationRequest();
        $this->AddRelationToRequest($Request, $ParentRows);
        
        return $Request;
    }
    /**
     * Relational\Request
     */
    protected abstract function NewRelationRequest();
}

?>