<?php

namespace Storm\Core\Object;

interface IRequest {
    const IRequestType = __CLASS__;
    
    public function GetEntityType();
    
    /**
     * @return IProperty[]
     */
    public function GetProperties();
    
    public function IsConstrained();
    /**
     * @return Constraints\IPredicate[]
     */
    public function GetPredicates();
    
    public function IsOrdered();
    
    /**
     * @return \SplObjectStorage
     */
    public function GetOrderedExpressionsAscendingMap();
    
    public function IsSingleEntity();
    public function IsRanged();
    public function GetRangeOffset();
    public function GetRangeAmount();    
}

?>