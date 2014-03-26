<?php

namespace Penumbra\Core\Object;

/**
 * The criteria represents the data nessecary to specify which entities
 * the criteria applys to.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface ICriteria {
    const ICriteriaType = __CLASS__;
    
    /**
     * The type of entities which the criteria represents.
     * 
     * @return string
     */
    public function GetEntityType();
    
    /**
     * Whether or not the criteria contains any predicates.
     * 
     * return boolean
     */
    public function IsConstrained();
    
    /**
     * @return Expression[]
     */
    public function GetPredicateExpressions();
    
    /**
     * Whether or not the criteria contains any order by expressions.
     * 
     * return boolean
     */
    public function IsOrdered();
    
    /**
     * Returns a SplObjectStorage with the an Expression to order by mapped to
     * a boolean representing whether or not the it is ascending.
     * 
     * @return \SplObjectStorage
     */
    public function GetOrderByExpressionsAscendingMap();
    
    /**
     * Whether or not the criteria contains an offset other than zero or a limit.
     * 
     * return boolean
     */
    public function IsRanged();
    
    /**
     * return int
     */
    public function GetRangeOffset();
    
    /**
     * return int|null
     */
    public function GetRangeAmount();    
}

?>