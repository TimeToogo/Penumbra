<?php

namespace Storm\Drivers\Base\Object\Properties;

use \Storm\Core\Object;
use \Storm\Core\Object\Domain;

abstract class RelationshipProperty extends Property implements Object\IRelationshipProperty {
    private $EntityType;
    private $IsIdentifying;
    private $OriginalValueStorageKey;
    public function __construct(Accessors\Accessor $Accessor, $EntityType, $IsIdentifying) {
        parent::__construct($Accessor);
        $this->EntityType = $EntityType;
        $this->IsIdentifying = $IsIdentifying;
        $this->OriginalValueStorageKey = $EntityType . $this->GetIdentifier();
    }
    
    final public function GetEntityType() {
        return $this->EntityType;
    }
    
    final public function IsIdentifying() {
        return $this->IsIdentifying;
    }
    
    final public function Revive(Domain $Domain, $PropertyValue, $Entity) {
        $RevivedPropertyValue = $this->ReviveValue($Domain, $Entity, $PropertyValue);
        if($RevivedPropertyValue instanceof Proxies\IProxy) {
            $Entity->{$this->OriginalValueStorageKey} = $RevivedPropertyValue->__CloneProxyInstance();
        }
        else {
            $Entity->{$this->OriginalValueStorageKey} = clone $RevivedPropertyValue;
        }
        $this->GetAccessor()->SetValue($Entity, $RevivedPropertyValue);
    }
    
    final public function ReviveValue(Domain $Domain, $Entity, $PropertyRevivalValue) {
        if($PropertyRevivalValue === null) {
            return $this->ReviveNull($Domain, $Entity);
        }
        if($PropertyRevivalValue instanceof RevivalData) {
            return $this->ReviveRevivalData($Domain, $Entity, $PropertyRevivalValue);
        }
        else if(is_callable($PropertyRevivalValue)) {
            return $this->ReviveCallable($Domain, $Entity, $PropertyRevivalValue);
        }
        else if(is_array($PropertyRevivalValue)) {
            if(count(array_filter($PropertyRevivalValue, function ($Value) { return $Value instanceof Object\RevivalData; })) === count($PropertyRevivalValue)) {
                return $this->ReviveArrayOfRevivalData($Domain, $Entity, $PropertyRevivalValue);
            }
            else if(count(array_filter($PropertyRevivalValue, function ($Value) { return is_callable($Value); })) === count($PropertyRevivalValue)) {
                return $this->ReviveArrayOfCallables($Domain, $Entity, $PropertyRevivalValue);
            }
        }
        
        throw new \Exception;//TODO:error message
    }
    
    protected function ReviveNull(Domain $Domain, $Entity) {
        throw new \Exception;//TODO:error message
    }
    
    protected function ReviveRevivalData(Domain $Domain, $Entity, RevivalData $RevivalData) {
        throw new \Exception;//TODO:error message
    }
    
    protected function ReviveCallable(Domain $Domain, $Entity, callable $Callback) {
        throw new \Exception;//TODO:error message
    }
    
    protected function ReviveArrayOfRevivalData(Domain $Domain, $Entity, array $RevivalDataArray) {
        throw new \Exception;//TODO:error message
    }
    
    protected function ReviveArrayOfCallables(Domain $Domain, $Entity, array $Callbacks) {
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
    
    final protected function IsEntityAltered($Entity) {
        if($Entity instanceof Proxies\IProxy) {
            return $Entity->__IsAltered();
        }
        else {
            return true;
        }
    }
}

?>
