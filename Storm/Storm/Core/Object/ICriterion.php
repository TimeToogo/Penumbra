<?php

namespace Storm\Core\Object;

interface ICriterion {
    const ICriterionType = __CLASS__;
    
    public function IsConstrained();
    /**
     * @return Expressions\Expression[]
     */
    public function GetPredicateExpressions();
    
    public function IsOrdered();
    /**
     * @return \SplObjectStorage
     */
    public function GetOrderExpressionsAscendingMap();
    
    public function IsRanged();
    public function GetRangeOffset();
    public function GetRangeAmount();    
}

?>