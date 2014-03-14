<?php

namespace Storm\Core\Relational;

/**
 * The base class for a select
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class Select {
    /**
     * @var Expression[] 
     */
    private $AggregatePredicateExpressions = [];
    
    /**
     * @var Expression[]
     */
    private $GroupByExpressions = [];
    
    /**
     * @var Criteria
     */
    private $Criteria;
    
    public function __construct(Criteria $Criteria) {
        $this->Criteria = $Criteria;
    }
    
    public abstract function GetSelectType();
        
    /**
     * @return ITable[]
     */
    final public function GetTables() {
        return $this->Criteria->GetAllTables();
    }
    
    /**
     * @return Criteria
     */
    final public function GetCriteria() {
        return $this->Criteria;
    }
        
    // <editor-fold defaultstate="collapsed" desc="Grouping">

    /**
     * @return boolean
     */
    final public function IsGrouped() {
        return count($this->GroupByExpressions) > 0;
    }

    /**
     * @return Expression[]
     */
    final public function GetGroupByExpressions() {
        return $this->GroupByExpressions;
    }

    final public function AddGroupByExpression(Expression $Expression) {
        $this->GroupByExpressions[] = $Expression;
    }


    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Aggregate Constraints">
    
    /**
     * @return boolean
     */
    final public function IsAggregateConstrained() {
        return count($this->AggregatePredicateExpressions) > 0;
    }

    /**
     * @return Expression[]
     */
    final public function GetAggregatePredicateExpressions() {
        return $this->AggregatePredicateExpressions;
    }

    final public function AddAggregatePredicateExpression(Expression $PredicateExpression) {
        $this->AggregatePredicateExpressions[] = $PredicateExpression;
    }

    // </editor-fold>
}

?>