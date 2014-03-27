<?php

namespace Penumbra\Drivers\Base\Object;

use Penumbra\Core\Object;

class Criteria implements Object\ICriteria {
    private $EntityType;
    private $PredicateExpressions;
    private $OrderByExpressionsAscendingMap;
    private $RangeOffset;
    private $RangeAmount;
    
    public function __construct(
            $EntityType,
            array $PredicateExpressions = [],
            \SplObjectStorage $OrderByExpressionsAscendingMap = null,
            $RangeOffset = 0,
            $RangeAmount = null) {
        $this->EntityType = $EntityType;
        $this->PredicateExpressions = $PredicateExpressions;
        $this->OrderByExpressionsAscendingMap = $OrderByExpressionsAscendingMap ?: new \SplObjectStorage();
        $this->RangeOffset = $RangeOffset;
        $this->RangeAmount = $RangeAmount;
    }
    
    final public function GetEntityType() {
        return $this->EntityType;
    }
    
    final public function IsConstrained() {
        return count($this->PredicateExpressions) > 0;
    }

    /**
     * @return Object\Expressions\Expression[]
     */
    final public function GetPredicateExpressions() {
        return $this->PredicateExpressions;
    }
    
    final public function IsOrdered() {
        return $this->OrderByExpressionsAscendingMap->count() > 0;
    }

    /**
     * @return \SplObjectStorage
     */
    final public function GetOrderByExpressionsAscendingMap() {
        return $this->OrderByExpressionsAscendingMap;
    }

    final public function IsRanged() {
        return $this->RangeOffset !== 0 || $this->RangeAmount !== null;
    }

    final public function GetRangeOffset() {
        return $this->RangeOffset;
    }
    
    final public function GetRangeAmount() {
        return $this->RangeAmount;
    }
}

?>