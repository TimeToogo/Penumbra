<?php

namespace Storm\Core\Object;

interface ICriterion {
    const ICriterionType = __CLASS__;
    
    public function GetEntityType();
    
    public function IsConstrained();
    /**
     * @return Expressions\Expression[]
     */
    public function GetPredicateExpressions();
    
    public function IsOrdered();
    /**
     * @return \SplObjectStorage
     */
    public function GetOrderByExpressionsAscendingMap();
    
    public function IsGrouped();
    /**
     * @return Expressions\Expression[]
     */
    public function GetGroupByExpressions();
    
    public function IsRanged();
    public function GetRangeOffset();
    public function GetRangeAmount();    
}

?>