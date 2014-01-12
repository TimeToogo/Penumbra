<?php

namespace Storm\Core\Relational;

class Criterion {
    private $Tables = array();
    private $PredicateExpressions = array();
    private $OrderByExpressionsAscendingMap;
    private $GroupByExpressions = array();
    private $RangeOffset;
    private $RangeAmount;
    
    public function __construct() {
        $this->OrderByExpressionsAscendingMap = new \SplObjectStorage();
        $this->RangeOffset = 0;
        $this->RangeAmount = null;
    }
    
    /**
     * @return Table[]
     */
    final public function GetTables() {
        $this->Tables;
    }    
    
    final public function AddTable(Table $Table) {
        $this->Tables[$Table->GetName()] = $Table;
    }
    
    final public function AddTables(array $Tables) {
        array_walk($Tables, [$this, 'AddTable']);
    }
    
    // <editor-fold defaultstate="collapsed" desc="Constraints">

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

    final public function IsRanged() {
        return $this->RangeOffset !== 0 || $this->RangeAmount !== null;
    }

    final public function GetRangeOffset() {
        return $this->RangeOffset;
    }

    final public function SetRangeOffset($RangeOffset) {
        if (!is_int($RangeOffset)) {
            throw new \InvalidArgumentException('$RangeOffset');
        }
        $this->RangeOffset = $RangeOffset;
    }


    final public function GetRangeAmount() {
        return $this->RangeAmount;
    }

    final public function SetRangeAmount($RangeAmount) {
        if (!is_int($RangeAmount) && $RangeAmount !== null) {
            throw new \InvalidArgumentException('$RangeOffset');
        }
        $this->RangeAmount = $RangeAmount;
    }


    // </editor-fold>

}

?>