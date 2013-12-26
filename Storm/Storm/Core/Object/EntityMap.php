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
     * @var IDataProperty[] 
     */
    private $DataProperties = array();
    /**
     * @var IEntityProperty[] 
     */
    private $EntityProperties = array();
    /**
     * @var ICollectionProperty[] 
     */
    private $CollectionProperties = array();
    /**
     * @var IProperty[] 
     */
    private $IdentityProperties = array();
    
    public function __construct() {
        $this->EntityType = $this->EntityType();
    }
    
    final public function InititalizeProperties(Domain $Domain) {
        $Registrar = new Registrar(IProperty::IPropertyType);
        $this->RegisterProperties($Domain, $Registrar);
        foreach($Registrar->GetRegistered() as $Property) {
            $this->AddProperty($Property);
        }
    }
    
    protected abstract function EntityType();
    protected abstract function RegisterProperties(Domain $Domain, Registrar $Registrar);
    
    private function AddProperty(IProperty $Property) {
        if($Property->GetEntityMap()) {
            if(!$Property->GetEntityMap()->Is($this)) {
                throw new \Exception('Property belongs to another EntityMap');
            }
        }
        $Property->SetEntityMap($this);
        $Identifier = $Property->GetIdentifier();
        
        if($Property instanceof IDataProperty) {
            $this->DataProperties[$Identifier] = $Property;
            if($Property->IsIdentity()) {
                $this->IdentityProperties[$Identifier] = $Property;
            }
        }
        else if($Property instanceof IEntityProperty) {
            $this->EntityProperties[$Identifier] = $Property;
        }
        else if($Property instanceof ICollectionProperty) {
            $this->CollectionProperties[$Identifier] = $Property;
        }
        else {
            throw new Exception;//TODO:error message
        }
        
        $this->Properties[$Identifier] = $Property;
    }
    
    private function VerifyEntity($Entity) {
        if(!($Entity instanceof $this->EntityType)) {
            throw new \InvalidArgumentException('$Entity must be of the type ' . $this->EntityType);
        }
    }
    
    final public function HasProperty($Identifier) {
        return isset($this->Properties[$Identifier]);
    }
    
    final public function HasIdentityProperty($Identifier) {
        return isset($this->IdentityProperties[$Identifier]);
    }
    
    final public function GetProperty($Identifier) {
        return $this->HasProperty($Identifier) ? $this->Properties[$Identifier] : null;
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
    
    /**
     * @return IEntityProperty[]
     */
    final public function GetEntityProperties() {
        return $this->EntityProperties;
    }
    
    /**
     * @return ICollectionProperty[]
     */
    final public function GetCollectionProperties() {
        return $this->CollectionProperties;
    }
    
    protected abstract function ConstructEntity();
    
    final public function HasIdentity($Entity) {
        foreach($this->Identity($Entity) as $Value) {
            if($Value !== null) {
                return true;
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
        foreach($this->IdentityProperties as $IdentityProperty) {
            $IdentityProperty->Store($Identity, $Entity);
        }
        
        return $Identity;
    }
    
    final public function RevivalData() {
        return new RevivalData($this);
    }
    
    final public function PersistanceData() {
        return new PersistenceData($this);
    }
    
    final public function Persist(UnitOfWork $UnitOfWork, $Entity) {
        $this->VerifyEntity($Entity);
        
        $PersistenceData = new PersistenceData($this);
        foreach($this->Properties as $Property) {
            $Property->Persist($UnitOfWork, $PersistenceData, $Entity);
        }
        
        return $PersistenceData;
    }
    
    final public function Apply(Domain $Domain, $Entity, PropertyData $PropertyData) {
        foreach($PropertyData as $PropertyIdentifier => $Value) {
            $this->Properties[$PropertyIdentifier]->Revive($Domain, $Entity, $Value);
        }
    }
    
    final public function ReviveEntities(Domain $Domain, array $RevivalDatas) {
        $Entities = array();
        foreach($RevivalDatas as $Key => $RevivalData) {
            $Entity = $this->ConstructEntity();
            $this->LoadEntity($RevivalData, $Entity);
            $Entities[$Key] = $Entity;
        }
        
        return $Entities;
    }
    
    
    final public function ReviveEntityInstances(Domain $Domain, Map $StateInstanceMap) {
        /*foreach($StateInstanceMap as $State) {
            $Instance = $StateInstanceMap[$State];
            $this->LoadEntity($State, $Instance);
        }*/
    }
    
    final public function LoadEntity(Domain $Domain, RevivalData $RevivalData, $Entity) {
        foreach($this->Properties as $Property) {
            $Property->Revive($Domain, $RevivalData, $Entity);
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