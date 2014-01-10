<?php

namespace Storm\Core\Relational;

class Criterion {
    private $PredicateExpressions = array();
    private $OrderByExpressionsAscendingMap;
    private $RangeOffset;
    private $RangeAmount;
    
    public function __construct() {
        $this->OrderByExpressionsAscendingMap = new \SplObjectStorage();
        $this->RangeOffset = 0;
        $this->RangeAmount = null;
    }
    
    final public function IsConstrained() {
        return count($this->PredicateExpressions) > 0;
    }
    
    /**
     * @return Expressions\Expression[]
     */
    final public function GetPredicateExpressions() {
        return $this->PredicateExpressions;
    }
    final public function AddPredicate(Expressions\Expression $PredicateExpression) {
        $this->PredicateExpressions[] = $PredicateExpression;
    }
    
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
    
    final public function IsRanged() {
        return $this->RangeOffset !== 0 || $this->RangeAmount !== null;
    }
    final public function GetRangeOffset() {
        return $this->RangeOffset;
    }
   final  public function SetRangeOffset($RangeOffset) {
       if(!is_int($RangeOffset)) {
           throw new \InvalidArgumentException('$RangeOffset');
       }
       $this->RangeOffset = $RangeOffset;
    }
    
    final public function GetRangeAmount() {
        return $this->RangeAmount;
    }
    final public function SetRangeAmount($RangeAmount) {
       if(!is_int($RangeAmount)) {
           throw new \InvalidArgumentException('$RangeOffset');
       }
        $this->RangeAmount = $RangeAmount;
    }
}

?>