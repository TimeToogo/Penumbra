<?php

namespace Storm\Core\Relational;

/**
 * The criterion represents the data nessecary to specify which rows should the operation apply to.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class Criterion {
    /**
     * @var ITable 
     */
    private $Table;
    
    /**
     * @var Join[] 
     */
    private $Joins = [];
    
    /**
     * @var ITable[] 
     */
    private $AllTables = [];
    
    /**
     * @var Expression[] 
     */
    private $PredicateExpressions = [];
    
    
    /**
     * @var Expression[] 
     */
    private $AggregatePredicateExpressions = [];
    
    /**
     * The order by expressions mapped to a boolean representing whether
     * or not is is ascending.
     * 
     * @var \SplObjectStorage
     */
    private $OrderByExpressionsAscendingMap;
    
    /**
     * @var Expression[]
     */
    private $GroupByExpressions = [];
    
    /**
     * @var int
     */
    private $RangeOffset;
    
    /**
     * @var int|null
     */
    private $RangeAmount;
    
    public function __construct(ITable $Table) {
        $this->Table = $Table;
        $this->AllTables[$Table->GetName()] = $Table;
        $this->OrderByExpressionsAscendingMap = new \SplObjectStorage();
        $this->RangeOffset = 0;
        $this->RangeAmount = null;
    }
    
    
    /**
     * @return ITable
     */
    final public function GetTable() {
        return $this->Table;
    }
    
    /**
     * @return ITable
     */
    final public function GetAllTables() {
        return $this->AllTables;
    }
    
    /**
     * @return ITable
     */
    final public function HasTable($TableName) {
        return isset($this->AllTables[$TableName]);
    }
    
    // <editor-fold defaultstate="collapsed" desc="Joins">
    
    /**
     * @return Join[]
     */
    final public function GetJoins() {
        return $this->Joins;
    }
    
    /**
     * Add a joined table to the criterion.
     * 
     * @param Join $Join The join to add
     * @return void
     */
    final public function AddJoin(Join $Join) {
        $this->Joins[] = $Join;
        $Table = $Join->GetTable();
        $this->AllTables[$Table->GetName()] = $Table;
    }
    
    /**
     * Add multiple joined tables to the criterion.
     * 
     * @param array $Joins The joins to add
     * @return void
     */
    final public function AddJoins(array $Joins) {
        array_walk($Joins, [$this, 'AddJoin']);
    }


    /**
     * @return boolean
     */
    final public function IsJoined() {
        return count($this->Joins) > 0;
    }

    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Constraints">
    
    /**
     * @return boolean
     */
    final public function IsConstrained() {
        return count($this->PredicateExpressions) > 0;
    }

    /**
     * @return Expression[]
     */
    final public function GetPredicateExpressions() {
        return $this->PredicateExpressions;
    }

    final public function AddPredicateExpression(Expression $PredicateExpression) {
        $this->PredicateExpressions[] = $PredicateExpression;
    }

    // </editor-fold>
        
    // <editor-fold defaultstate="collapsed" desc="Ordering">

    /**
     * @return boolean
     */
    final public function IsOrdered() {
        return $this->OrderByExpressionsAscendingMap->count() > 0;
    }

    /**
     * @return \SplObjectStorage
     */
    final public function GetOrderedExpressionsAscendingMap() {
        return $this->OrderByExpressionsAscendingMap;
    }

    final public function AddOrderByExpression(Expression $Expression, $Ascending) {
        $this->OrderByExpressionsAscendingMap[$Expression] = $Ascending;
    }


    // </editor-fold>
        
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
        
    // <editor-fold defaultstate="collapsed" desc="Ranging">

    /**
     * @return boolean
     */
    final public function IsRanged() {
        return $this->RangeOffset !== 0 || $this->RangeAmount !== null;
    }

    /**
     * @return int
     */
    final public function GetRangeOffset() {
        return $this->RangeOffset;
    }
    
    /**
     * Set the range offset.
     * 
     * @param int $RangeOffset
     * @throws \InvalidArgumentException If the parameter is not an integer
     */
    final public function SetRangeOffset($RangeOffset) {
        if (!is_int($RangeOffset)) {
            throw new \Storm\Core\UnexpectedValueException(
                    'The supplied range offset must be a valid integer: %s given',
                    \Storm\Core\Utilities::GetTypeOrClass($RangeOffset));
        }
        $this->RangeOffset = $RangeOffset;
    }

    /**
     * @return int|null
     */
    final public function GetRangeAmount() {
        return $this->RangeAmount;
    }

    /**
     * Set the range amount.
     * 
     * @param type $RangeAmount Specify null to remove the limit
     * @throws \InvalidArgumentException If the parameter is not a integer or null
     */
    final public function SetRangeAmount($RangeAmount) {
        if (!is_int($RangeAmount) && $RangeAmount !== null) {
            throw new \Storm\Core\UnexpectedValueException(
                    'The supplied range amount must be a valid integer or null: %s given',
                    \Storm\Core\Utilities::GetTypeOrClass($RangeAmount));
        }
        $this->RangeAmount = $RangeAmount;
    }

    // </editor-fold>

}

?>