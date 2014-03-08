<?php

namespace Storm\Drivers\Base\Object;

use Storm\Core\Object;

class Criterion implements Object\ICriterion {
    private $EntityType;
    private $PredicateExpressions;
    private $OrderByExpressionsAscendingMap;
    private $RangeOffset;
    private $RangeAmount;
    
    public function __construct($EntityType) {
        $this->EntityType = $EntityType;
        $this->PredicateExpressions = [];
        $this->OrderByExpressionsAscendingMap = new \SplObjectStorage();
        $this->GroupByExpressions = [];
        $this->RangeOffset = 0;
        $this->RangeAmount = null;
    }
    
    final public function GetEntityType() {
        return $this->EntityType;
    }
    
    // <editor-fold defaultstate="collapsed" desc="Constaints">
    
    final public function IsConstrained() {
        return count($this->PredicateExpressions) > 0;
    }

    /**
     * @return Object\Expressions\Expression[]
     */
    final public function GetPredicateExpressions() {
        return $this->PredicateExpressions;
    }

    final public function AddPredicate(Object\Expressions\Expression $PredicateExpression) {
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
    final public function GetOrderByExpressionsAscendingMap() {
        return $this->OrderByExpressionsAscendingMap;
    }

    final public function AddOrderByExpression(Object\Expressions\Expression $Expression, $Ascending) {
        $this->OrderByExpressionsAscendingMap[$Expression] = $Ascending;
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
        $this->RangeOffset = $RangeOffset;
    }


    final public function GetRangeAmount() {
        return $this->RangeAmount;
    }


    final public function SetRangeAmount($RangeAmount) {
        $this->RangeAmount = $RangeAmount;
    }


    // </editor-fold>

}

?>