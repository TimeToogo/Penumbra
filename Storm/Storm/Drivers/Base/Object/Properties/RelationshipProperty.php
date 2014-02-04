<?php

namespace Storm\Drivers\Base\Object\Properties;

use \Storm\Core\Object;
use \Storm\Core\Object\Domain;
use \Storm\Drivers\Base\Object\LazyRevivalData;
use \Storm\Drivers\Base\Object\MultipleLazyRevivalData;

abstract class RelationshipProperty extends Property implements Object\IRelationshipProperty {
    private $EntityType;
    private $IsIdentifying;
    private $OriginalValueStorageKey;
    private $BackReferenceProperty;
    
    public function __construct(
            Accessors\Accessor $Accessor, 
            $EntityType, 
            $IsIdentifying,
            Object\IProperty $BackReferenceProperty = null) {
        parent::__construct($Accessor);
        $this->EntityType = $EntityType;
        $this->IsIdentifying = $IsIdentifying;
        $this->BackReferenceProperty = $BackReferenceProperty;
        $this->OriginalValueStorageKey = $EntityType . $this->GetIdentifier();
    }
    
    final public function GetEntityType() {
        return $this->EntityType;
    }
    
    final public function IsIdentifying() {
        return $this->IsIdentifying;
    }
    
    /**
     * @return Object\IProperty
     */
    final public function GetBackReferenceProperty() {
        return $this->BackReferenceProperty;
    }
    
    final public function Revive(Domain $Domain, $PropertyValue, $Entity) {
        $RevivedPropertyValue = $this->ReviveValue($Domain, $Entity, $PropertyValue);
        if($RevivedPropertyValue instanceof Proxies\IProxy) {
            $Entity->{$this->OriginalValueStorageKey} = $RevivedPropertyValue->__CloneProxyInstance();
        }
        else {
            $Entity->{$this->OriginalValueStorageKey} = is_object($RevivedPropertyValue) ?
                    clone $RevivedPropertyValue : $RevivedPropertyValue;
        }
        $this->GetAccessor()->SetValue($Entity, $RevivedPropertyValue);
    }
    
    final public function ReviveValue(Domain $Domain, $Entity, $PropertyRevivalValue) {
        if($PropertyRevivalValue === null) {
            return $this->ReviveNull($Domain, $Entity);
        }
        if($PropertyRevivalValue instanceof Object\RevivalData) {
            $this->AddBackReference($PropertyRevivalValue, $Entity);
            return $this->ReviveRevivalData($Domain, $Entity, $PropertyRevivalValue);
        }
        if($PropertyRevivalValue instanceof LazyRevivalData) {
            $this->AddBackReference($PropertyRevivalValue->GetAlreadyKnownRevivalData(), $Entity);
            return $this->ReviveLazyRevivalData($Domain, $Entity, $PropertyRevivalValue);
        }
        if($PropertyRevivalValue instanceof MultipleLazyRevivalData) {
            $this->AddBackReference($PropertyRevivalValue->GetAlreadyKnownRevivalData(), $Entity);
            return $this->ReviveMultipleLazyRevivalData($Domain, $Entity, $PropertyRevivalValue);
        }
        else if(is_array($PropertyRevivalValue)) {
            if($this->IsAll($PropertyRevivalValue, function ($I) { return $I instanceof Object\RevivalData; })) {
                $this->AddBackReferences($PropertyRevivalValue, $Entity);
                return $this->ReviveArrayOfRevivalData($Domain, $Entity, $PropertyRevivalValue);
            }
            if($this->IsAll($PropertyRevivalValue, function ($I) { return $I instanceof LazyRevivalData; })) {
                foreach($PropertyRevivalValue as $LazyRevivalData) {
                    $this->AddBackReference($LazyRevivalData->GetAlreadyKnownRevivalData(), $Entity);
                }
                return $this->ReviveArrayOfLazyRevivalData($Domain, $Entity, $PropertyRevivalValue);
            }
        }
        
        throw new \Exception;//TODO:error message
    }
    
    private function IsAll(array $Values, callable $Filter) {
        return count(array_filter($Values, $Filter)) === count($Values);
    }
    
    private function AddBackReference(Object\RevivalData $RevivalData, $ParentEntity) {
        if($this->BackReferenceProperty !== null) {
            $RevivalData[$this->BackReferenceProperty] = $ParentEntity;
        }
    }
    private function AddBackReferences(array $RevivalDataArray, $ParentEntity) {
        if($this->BackReferenceProperty !== null) {
            foreach($RevivalDataArray as $RevivalData) {
                $this->AddBackReference($RevivalData, $ParentEntity);
            }
        }
    }
    
    protected function ReviveNull(Domain $Domain, $Entity) {
        throw new \Exception;//TODO:error message
    }
    
    protected function ReviveRevivalData(Domain $Domain, $Entity, Object\RevivalData $RevivalData) {
        throw new \Exception;//TODO:error message
    }
    
    protected function ReviveLazyRevivalData(Domain $Domain, $Entity, LazyRevivalData $LazyRevivalData) {
        throw new \Exception;//TODO:error message
    }
    
    protected function ReviveMultipleLazyRevivalData(Domain $Domain, $Entity, MultipleLazyRevivalData $MultipleLazyRevivalData) {
        throw new \Exception;//TODO:error message
    }
    
    protected function ReviveArrayOfRevivalData(Domain $Domain, $Entity, array $RevivalDataArray) {
        throw new \Exception;//TODO:error message
    }
    
    protected function ReviveArrayOfLazyRevivalData(Domain $Domain, $Entity, array $LazyRevivalDataArray) {
        throw new \Exception;//TODO:error message
    }
    
    
    final protected function GetEntityRelationshipData($ParentEntity) {
        return [
                $this->GetAccessor()->GetValue($ParentEntity),
                $this->HasOriginalValue($ParentEntity),
                $this->GetOriginalValue($ParentEntity),
            ];
    }
    
    final protected function IsValidEntity($Entity) {
        return $Entity instanceof $this->EntityType;
    }
    
    final protected function GetOriginalValue($Entity) {
        return property_exists($Entity, $this->OriginalValueStorageKey) ? $Entity->{$this->OriginalValueStorageKey} : null;
    }
    
    final protected function HasOriginalValue($Entity) {
        return property_exists($Entity, $this->OriginalValueStorageKey);
    }
}

?>
