<?php

namespace Storm\Core\Relational;

/**
 * The criterion represents the data nessecary to specify which rows should the operation apply to.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class Criterion {
    /**
     * @var ITable[] 
     */
    private $Tables = array();
    
    /**
     * @var Expressions\Expression[] 
     */
    private $PredicateExpressions = array();
    
    /**
     * The order by expressions mapped to a boolean representing whether
     * or not is is ascending.
     * 
     * @var \SplObjectStorage
     */
    private $OrderByExpressionsAscendingMap;
    
    /**
     * @var Expressions\Expression[]
     */
    private $GroupByExpressions = array();
    
    /**
     * @var int
     */
    private $RangeOffset;
    
    /**
     * @var int|null
     */
    private $RangeAmount;
    
    public function __construct() {
        $this->OrderByExpressionsAscendingMap = new \SplObjectStorage();
        $this->RangeOffset = 0;
        $this->RangeAmount = null;
    }
    
    /**
     * @return ITable[]
     */
    final public function GetTables() {
        return $this->Tables;
    }    
    
    /**
     * Add a table to the criterion.
     * 
     * @param ITable $Table The table to add
     * @return void
     */
    final public function AddTable(ITable $Table) {
        $this->Tables[$Table->GetName()] = $Table;
    }
    
    /**
     * Add an array of tables to the criterion
     * 
     * @param ITable[] $Tables The tables to add
     * @return void
     */
    final public function AddTables(array $Tables) {
        array_walk($Tables, [$this, 'AddTable']);
    }
    
    // <editor-fold defaultstate="collapsed" desc="Constraints">
    
    /**
     * @return boolean
     */
    final public function IsConstrained() {
        return count($this->PredicateExpressions) > 0;
    }

    /**
     * @return Expressions\Expression[]
     */
    final public function GetPredicateExpressions() {
        return $this->PredicateExpressions;
    }

    final public function AddPredicateExpression(Expressions\Expression $PredicateExpression) {
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

    final public function AddOrderByExpression(Expressions\Expression $Expression, $Ascending) {
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
     * @return Expressions\Expression[]
     */
    final public function GetGroupByExpressions() {
        return $this->GroupByExpressions;
    }

    final public function AddGroupByExpression(Expressions\Expression $Expression) {
        $this->GroupByExpressions[] = $Expression;
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
            throw new \InvalidArgumentException('$RangeOffset');
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
            throw new \InvalidArgumentException('$RangeOffset');
        }
        $this->RangeAmount = $RangeAmount;
    }

    // </editor-fold>

}

?>