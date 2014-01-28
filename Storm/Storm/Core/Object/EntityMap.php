<?php

namespace Storm\Core\Object;

use \Storm\Core\Containers\Registrar;
use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping\DomainDatabaseMap;

/**
 * The entity map represents the properties of a type of entity.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class EntityMap implements \IteratorAggregate {
    use \Storm\Core\Helpers\Type;   
    
    /**
     * @var string
     */
    private $EntityType;
    
    /**
     * The properties of entity.
     * 
     * @var IProperty[] 
     */
    private $Properties = array();
    
    /**
     * The properties containing the entity data.
     * 
     * @var IDataProperty[] 
     */
    private $DataProperties = array();
    
    /**
     * The properties containing the related entities.
     * 
     * @var IEntityProperty[] 
     */
    private $EntityProperties = array();
    
    /**
     * The properties containing the related entity collections.
     * 
     * @var ICollectionProperty[] 
     */
    private $CollectionProperties = array();
    
    /**
     * The properties containing the entity's identity.
     * 
     * @var IDataProperty[] 
     */
    private $IdentityProperties = array();
    
    public function __construct() {
        $this->EntityType = $this->EntityType();
    }
    
    /**
     * Initializes the properties of the entity within the context of the parent domain.
     * 
     * @param Domain $Domain The parent domain
     * @return void
     */
    final public function InititalizeProperties(Domain $Domain) {
        $Registrar = new Registrar(IProperty::IPropertyType);
        $this->RegisterProperties($Domain, $Registrar);
        foreach($Registrar->GetRegistered() as $Property) {
            $this->AddProperty($Property);
        }
    }
    
    /**
     * This method should be implemented such that it returns the type of entity
     * that this map represents.
     * 
     * @return string
     */
    protected abstract function EntityType();
    
    /**
     * This method should be implemented such that it registers the properties of 
     * the entity.
     * 
     * @return void
     */
    protected abstract function RegisterProperties(Domain $Domain, Registrar $Registrar);
    
    /**
     * Adds a property to the given entity map.
     * 
     * @param \Storm\Core\Object\IProperty $Property The property to add
     * @return void
     * @throws \Exception
     */
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
            throw new \Exception;//TODO:error message
        }
        
        $this->Properties[$Identifier] = $Property;
    }
    
    /**
     * Verifies an object to be of the type represented by this entity map.
     * 
     * @param object $Entity The entity to verify
     * @throws \InvalidArgumentException
     */
    private function VerifyEntity($Entity) {
        if(!($Entity instanceof $this->EntityType)) {
            throw new \InvalidArgumentException('$Entity must be of the type ' . $this->EntityType);
        }
    }
    
    /**
     * Whether or not this map contains a property withthe supplied identifier
     * 
     * @param string $Identifier The property identifier
     * @return boolean
     */
    final public function HasProperty($Identifier) {
        return isset($this->Properties[$Identifier]);
    }
    
    /**
     * Whether or not this map contains a property with the supplied identifier
     * 
     * @param string $Identifier The property identifier
     * @return boolean
     */
    final public function HasIdentityProperty($Identifier) {
        return isset($this->IdentityProperties[$Identifier]);
    }
    
    /**
     * Whether or not this map contains a relationship property with the supplied identifier
     * 
     * @param string $Identifier The property identifier
     * @return void
     */
    final public function HasRelationshipProperty($Identifier) {
        return isset($this->EntityProperties[$Identifier]) || isset($this->CollectionProperties[$Identifier]);
    }
    
    /**
     * Gets a property by its identifier
     * 
     * @param string $Identifier The property identifier
     * @return IProperty|null The matched property
     */
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
     * @return string
     */
    final public function GetEntityType() {
        return $this->EntityType;
    }
    
    final public function getIterator() {
        return new \ArrayIterator($this->Properties);
    }
    
    /**
     * Whether or not this and the supplied entity map represent the same entity.
     * 
     * @param EntityMap $OtherEntityMap Another entity map
     * @return boolean
     */
    final public function Is(EntityMap $OtherEntityMap) {
        return $this->EntityType === $OtherEntityMap->EntityType;
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
    
    /**
     * This method should be implemented such that it returns a blank new instance of 
     * the entity
     * 
     * @return object The constructed entity instance
     */
    protected abstract function ConstructEntity();
    
    /**
     * Whether or not the entity has a full identity.
     * 
     * @param object $Entity The entity to check
     * @return boolean
     */
    final public function HasIdentity($Entity) {
        foreach($this->Identity($Entity) as $Value) {
            if($Value === null) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * If the entity is null returns a new blank identity otherwise returns the identity
     * of the supplied entity.
     * 
     * @param object $Entity
     * @return array The entity's identity
     */
    final public function Identity($Entity) {        
        $this->VerifyEntity($Entity);
        
        $Identity = array();
        foreach($this->IdentityProperties as $Identifier => $IdentityProperty) {
            $Identity[$Identifier] = $IdentityProperty->GetValue($Entity);
        }
        
        return $Identity;
    }
    
    /**
     * Persists an entity's relationships to the supplied unit of work and returns the persistence data.
     * 
     * @param UnitOfWork $UnitOfWork The unit of work to persist to
     * @param object $Entity The entity to persist
     * @return PersistenceData The persistence data of the entity
     */
    final public function Persist(UnitOfWork $UnitOfWork, $Entity) {
        $this->VerifyEntity($Entity);
        
        $PersistenceData = new \ArrayObject();
        foreach($this->DataProperties as $Identifier => $DataProperty) {
            $PersistenceData[$Identifier] = $DataProperty->GetValue($Entity);
        }
        foreach($this->EntityProperties as $Identifier => $EntityProperty) {
            $PersistenceData[$Identifier] = $EntityProperty->Persist($UnitOfWork, $Entity);
        }
        foreach($this->CollectionProperties as $Identifier => $CollectionProperty) {
            $PersistenceData[$Identifier] = $CollectionProperty->Persist($UnitOfWork, $Entity);
        }
        
        return $PersistenceData;
    }
    
    /**
     * Discards an entity's relationships to the supplied unit of work and returns the discardence data.
     * 
     * @param UnitOfWork $UnitOfWork The unit of work to discard from
     * @param object $Entity The entity to discard
     * @return array The discardence data of the entity
     */
    final public function Discard(UnitOfWork $UnitOfWork, $Entity) {
        $this->VerifyEntity($Entity);
        
        $DiscardingData = array();
        foreach($this->IdentityProperties as $Identifier => $IdentityProperty) {
            $DiscardingData[$Identifier] = $IdentityProperty->GetValue($Entity);
        }
        foreach($this->EntityProperties as $Identifier => $EntityProperty) {
            $DiscardingData[$Identifier] = $EntityProperty->Discard($UnitOfWork, $Entity);
        }
        foreach($this->CollectionProperties as $Identifier => $CollectionProperty) {
            $DiscardingData[$Identifier] = $CollectionProperty->Discard($UnitOfWork, $Entity);
        }
        
        return $DiscardingData;
    }
    
    /**
     * Applies the supplied property data to the supplied entity instance.
     * 
     * @param Domain $Domain The object domain to revive in entity in.
     * @param object $Entity The entity to apply the property data
     * @param array $PropertyData The property data apply
     * @return void
     */
    final public function Apply(Domain $Domain, $Entity, array $PropertyData) {
        foreach($PropertyData as $PropertyIdentifier => $Value) {
            if(isset($this->DataProperties[$PropertyIdentifier])) {
                $this->DataProperties[$PropertyIdentifier]->ReviveValue($Value, $Entity);
            }
            else if(isset($this->EntityProperties[$PropertyIdentifier])) {
                $this->EntityProperties[$PropertyIdentifier]->Revive($Domain, $Value, $Entity);
            }
            else if(isset($this->CollectionProperties[$PropertyIdentifier])) {
                $this->CollectionProperties[$PropertyIdentifier]->Revive($Domain, $Value, $Entity);
            }
        }
    }
    
    /**
     * Revives an array of entities from the supplied array of revival data.
     * 
     * @param string $EntityType The type of entities to revive
     * @param array[] $RevivalDatas The array of revival data
     * @return object[] The revived entities
     */
    final public function ReviveEntities(Domain $Domain, array $RevivalDatas) {
        $Entities = array();
        foreach($RevivalDatas as $Key => $RevivalData) {
            $Entity = $this->ConstructEntity();
            $this->Apply($Domain, $Entity, $RevivalData);
            $Entities[$Key] = $Entity;
        }
        
        return $Entities;
    }
    
    /**
     * Loads an entity instance with the supplied revival data.
     * 
     * @param array $RevivalData The revival data to load the entity with
     * @param object $Entity The entity to load
     * @return void
     */
    final public function LoadEntity(Domain $Domain, array $RevivalData, $Entity) {
        $this->Apply($Domain, $Entity, $RevivalData);
    }
}

?>