<?php

namespace Storm\Core\Object;

use \Storm\Core\Containers\Registrar;
use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping\DomainDatabaseMap;

abstract class EntityMap implements \IteratorAggregate {
    use \Storm\Core\Helpers\Type;   
    
    private $EntityType;
    /**
     * @var IProperty[] 
     */
    private $Properties = array();
    /**
     * @var IProperty[] 
     */
    private $IdentityProperties = array();
    
    public function __construct() {
        $this->EntityType = $this->EntityType();
        
        $Registrar = new Registrar(IProperty::IPropertyType);
        $this->RegisterProperties($Registrar);
        foreach($Registrar->GetRegistered() as $Property) {
            $this->AddProperty($Property);
        }
    }
    protected abstract function EntityType();
    protected abstract function RegisterProperties(Registrar $Registrar);
    
    final protected function AddProperty(IProperty $Property) {
        $this->Properties[$Property->GetName()] = $Property;
        if($Property->IsIdentity())
            $this->IdentityProperties[$Property->GetName()]  = $Property;
    }
    
    private function VerifyEntity($Entity) {
        if(!($Entity instanceof $this->EntityType))
            throw new \InvalidArgumentException('$Entity must be of the type ' . $this->EntityType);
    }
    
    final public function HasProperty($Name) {
        return isset($this->Properties[$Name]);
    }
    
    final public function HasIdentityProperty($Name) {
        return isset($this->IdentityProperties[$Name]);
    }
    
    final public function GetProperty($Name) {
        return $this->HasProperty($Name) ? $this->Properties[$Name] : null;
    }
    
    /**
     * @return IProperty[]
     */
    final public function GetIdentityProperties() {
        return $this->IdentityProperties;
    }
    
    /**
     * @return IProperty[]
     */
    final public function GetProperties() {
        return $this->Properties;
    }
    
    protected abstract function ConstructEntity();
    
    final public function HasIdentity($Entity) {
        foreach($this->Properties as $Property) {
            if($Property->CanGetValue() && $Property->IsIdentity()) {
                if($Property->GetValue($Entity) !== null) {
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * @param object $Entity
     * @return Identity
     */
    final public function Identity($Entity = null) {
        if($Entity === null)
            return new Identity($this);
        
        $this->VerifyEntity($Entity);
        
        $Identity = new Identity($this);
        foreach($this->Properties as $Property) {
            if($Property->CanGetValue() && $Property->IsIdentity())
                $Identity[$Property] = $Property->GetValue($Identity);
        }
        
        return $Identity;
    }
    
    final public function SetIdentity($Entity, Identity $Identity) {
        $this->VerifyEntity($Entity);
        if($Identity->GetEntityMap() !== $this)
            throw new \InvalidArgumentException('$Identity must be of this EntityMap');
        
        foreach($this->Properties as $Property) {
            if($Property->CanSetValue() && $Property->IsIdentity() && isset($Identity[$Property])) {
                $Value = $Identity[$Property];
                $Property->SetValue($Entity, $Value);
            }
        }
    }
    
    final public function State($Entity = null, UnitOfWork $UnitOfWork = null) {
        if($Entity === null)
            return new State($this);
        
        $this->VerifyEntity($Entity);
        
        $State = new State($this);
        foreach($this->Properties as $Property) {
            $Property->Persist($UnitOfWork, $State, $Entity);
        }
        
        return $State;
    }
    
    final public function Apply($Entity, PropertyData $PropertyData) {
        foreach($PropertyData as $PropertyName => $Value) {
            $this->Properties[$PropertyName]->Revive($Entity, $Value);
        }
    }
    
    final public function ReviveEntities(array $EntityStates) {
        $Entities = array();
        foreach($EntityStates as $Key => $EntityState) {
            $Entity = $this->ConstructEntity();
            $this->LoadEntity($EntityState, $Entity);
            $Entities[$Key] = $Entity;
        }
        
        return $Entities;
    }
    
    
    final public function ReviveEntityInstances(Map $StateInstanceMap) {
        foreach($StateInstanceMap as $State) {
            $Instance = $StateInstanceMap[$State];
            $this->LoadEntity($State, $Instance);
        }
    }
    
    private function LoadEntity(State $State, $Instance) {
        foreach($this->Properties as $Property) {
            if(isset($State[$Property])) {
                $Value = $State[$Property];
                $Property->Revive($Instance, $Value);
            }
        }
    }
    
    final public function Persist(UnitOfWork $UnitOfWork, $Entity) {
        foreach($this->Properties as $Property) {
            $Property->Persist($UnitOfWork, $Entity);
        }
    }

    final public function Discard(UnitOfWork $UnitOfWork, $Entity) {
        foreach($this->Properties as $Property) {
            $Property->Discard($UnitOfWork, $Entity);
        }
    }
    
    final public function DiscardWhere(UnitOfWork $UnitOfWork, IRequest $Request) {
        $UnitOfWork->DiscardWhere($Request);
    }
    
    final public function GetEntityType() {
        return $this->EntityType;
    }
    
    final public function getIterator() {
        return new \ArrayIterator($this->Properties);
    }
}

?>