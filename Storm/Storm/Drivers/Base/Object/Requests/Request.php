<?php

namespace Storm\Drivers\Base\Object\Requests;

use \Storm\Core\Object;

class Request implements Object\IRequest {
    private $EntityType;
    private $Properties = array();
    private $Predicates;
    private $OrderedExpressionsAscendingMap;
    private $IsSingleEntity;
    private $RangeOffset;
    private $RangeAmount;
    
    public function __construct($EntityOrType, array $Properties, $IsSingleEntity) {
        if(is_object($EntityOrType))
            $EntityOrType = get_class($EntityOrType);
        $this->EntityType = $EntityOrType;
        foreach($Properties as $Property) {
            $this->AddProperty($Property);
        }
        $this->Predicates = array();
        $this->OrderedExpressionsAscendingMap = new \SplObjectStorage();
        $this->IsSingleEntity = $IsSingleEntity;
        $this->RangeOffset = 0;
        $this->RangeAmount = null;
    }
    
    final public function IsSingleEntity() {
        return $this->IsSingleEntity;
    }

    final public function GetEntityType() {
        return $this->EntityType;
    }
    
    final public function GetProperties() {
        return $this->Properties;
    }
    
    final public function IsConstrained() {
        return count($this->Predicates) > 0;
    }
    
    final public function AddProperty(Object\IProperty $Property) {
        $this->Properties[$Property->GetIdentifier()] = $Property;
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
    final public function GetPredicates() {
        return $this->Predicates;
    }
    final public function AddPredicate(Object\Constraints\Predicate $Predicate) {
        if($Predicate->GetEntityType() !== $this->EntityType) {
            throw new \InvalidArgumentException('$Predicate must be of entity type: ' . $this->EntityType);
        }
        else {
            $this->Predicates[] = $Predicate;
        }
    }
    

    final public function IsOrdered() {
        return $this->OrderedExpressionsAscendingMap->count() > 0;
    }
    /**
     * @return \SplObjectStorage
     */
    final public function GetOrderedExpressionsAscendingMap() {
        return $this->OrderedExpressionsAscendingMap;
    }
    final public function AddOrderByExpression(Object\Expressions\Expression $Expression, $Ascending) {
        $this->OrderedExpressionsAscendingMap[$Expression] = $Ascending;
    }
    
    final public function IsRanged() {
        if(!$this->IsSingleEntity)
            return $this->RangeOffset !== 0 || $this->RangeAmount !== null;
        else 
            return true;
    }
    
    final public function GetRangeOffset() {
        if($this->IsSingleEntity)
            return 0;
        else
            return $this->RangeOffset;
    }
    
    final  public function SetRangeOffset($RangeOffset) {
        $this->RangeOffset = $RangeOffset;
    }
    
    final public function GetRangeAmount() {
        if($this->IsSingleEntity)
            return 1;
        else
            return $this->RangeAmount;
    }
    
    final public function SetRangeAmount($RangeAmount) {
        $this->RangeAmount = $RangeAmount;
    }
}

?>