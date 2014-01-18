<?php

namespace Storm\Core\Object;

/**
 * The criterion represents the data nessecary to specify which entities
 * the criterion applys to.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface ICriterion {
    const ICriterionType = __CLASS__;
    
    /**
     * The type of entities which the criterion represents.
     * 
     * @return string
     */
    public function GetEntityType();
    
    /**
     * Whether or not the criterion contains any predicates.
     * 
     * return boolean
     */
    public function IsConstrained();
    
    /**
     * @return Expressions\Expression[]
     */
    public function GetPredicateExpressions();
    
    /**
     * Whether or not the criterion contains any order by expressions.
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
     * Whether or not the criterion contains any group by expressions.
     * 
     * return boolean
     */
    public function IsGrouped();
    
    /**
     * @return Expressions\Expression[]
     */
    public function GetGroupByExpressions();
    
    /**
     * Whether or not the criterion contains an offset other than zero or a limit.
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