<?php

namespace Storm\Drivers\Base\Object;

use Storm\Core\Object;

class Criterion implements Object\ICriterion {
    private $PredicateExpressions;
    private $OrderExpressionsAscendingMap;
    private $RangeOffset;
    private $RangeAmount;
    
    public function __construct() {
        $this->PredicateExpressions = array();
        $this->OrderExpressionsAscendingMap = new \SplObjectStorage();
        $this->RangeOffset = 0;
        $this->RangeAmount = null;
    }
    
    final public function IsConstrained() {
        return count($this->PredicateExpressions) > 0;
    }
    
    /**
     * @return Constraints\Predicate
     */
    final public function Predicate() {
        return Constraints\Predicate::On($this->EntityType);
    }
    
    /**
     * @return Constraints\Predicate[]
     */
    final public function GetPredicateExpressions() {
        return $this->PredicateExpressions;
    }
    final public function AddPredicate(Object\Expressions\Expression $PredicateExpression) {
        $this->PredicateExpressions[] = $PredicateExpression;
    }
    
    final public function IsOrdered() {
        return $this->OrderExpressionsAscendingMap->count() > 0;
    }
    /**
     * @return \SplObjectStorage
     */
    final public function GetOrderExpressionsAscendingMap() {
        return $this->OrderExpressionsAscendingMap;
    }
    final public function AddOrderByExpression(Object\Expressions\Expression $Expression, $Ascending) {
        $this->OrderExpressionsAscendingMap[$Expression] = $Ascending;
    }
    
    final public function IsRanged() {
        return $this->RangeOffset !== 0 || $this->RangeAmount !== null;
    }
    
    final public function GetRangeOffset() {
        return $this->RangeOffset;
    }
    
    final  public function SetRangeOffset($RangeOffset) {
        $this->RangeOffset = $RangeOffset;
    }
    
    final public function GetRangeAmount() {
        return $this->RangeAmount;
    }
    
    final public function SetRangeAmount($RangeAmount) {
        $this->RangeAmount = $RangeAmount;
    }

}

?>