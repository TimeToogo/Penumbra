<?php

namespace Storm\Core\Object;

use \Storm\Core\Containers\Registrar;
use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping\DomainDatabaseMap;

/**
 *{@inheritDoc}
 */
abstract class EntityMap implements IEntityMap {
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
     * The properties representing related entities.
     * 
     * @var IRelationshipProperty[] 
     */
    private $RelationshipProperties = array();
    
    /**
     * The properties containing a related entity.
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
     * {@inheritDoc}
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
     * @throws InvalidPropertyException
     */
    private function AddProperty(IProperty $Property) {
        if($Property->GetEntityMap()) {
            if(!$Property->GetEntityMap()->Is($this)) {
                throw new InvalidPropertyException(
                        'The supplied property is registered with another entity map %s.',
                        get_class($Property->GetEntityMap()));
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
            throw new InvalidPropertyException(
                    'The supplied property must be of type %s, %s, %s: %s given',
                    IDataProperty::IDataPropertyType,
                    IEntityProperty::IEntityPropertyType,
                    ICollectionProperty::ICollectionPropertyType,
                    get_class($Property));
        }
        if($Property instanceof IRelationshipProperty) {
            $this->RelationshipProperties[$Identifier] = $Property;
        }
        
        $this->Properties[$Identifier] = $Property;
    }
    
    /**
     * Verifies an object to be of the type represented by this entity map.
     * 
     * @param object $Entity The entity to verify
     * @throws TypeMismatchException
     */
    private function VerifyEntity($Method, $Entity) {
        if(!($Entity instanceof $this->EntityType)) {
            throw new TypeMismatchException(
                    'The supplied entity to %s must be of the type %s: %s given',
                    $Method,
                    $this->EntityType,
                    \Storm\Core\Utilities::GetTypeOrClass($Entity));
        }
    }
    
    /**
     * {@inheritDoc}
     */
    final public function HasProperty($Identifier) {
        return isset($this->Properties[$Identifier]);
    }
    
    /**
     * {@inheritDoc}
     */
    final public function HasIdentityProperty($Identifier) {
        return isset($this->IdentityProperties[$Identifier]);
    }
    
    /**
     * {@inheritDoc}
     */
    final public function HasRelationshipProperty($Identifier) {
        return isset($this->EntityProperties[$Identifier]) || isset($this->CollectionProperties[$Identifier]);
    }
    
    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    final public function GetEntityType() {
        return $this->EntityType;
    }
    
    /**
     * {@inheritDoc}
     */
    final public function Is(IEntityMap $OtherEntityMap) {
        return $this->EntityType === $OtherEntityMap->EntityType;
    }
    
    /**
     * {@inheritDoc}
     */
    final public function GetProperties() {
        return $this->Properties;
    }
    
    /**
     * {@inheritDoc}
     */
    final public function GetRelationshipProperties() {
        return $this->RelationshipProperties;
    }
    
    /**
     * {@inheritDoc}
     */
    final public function GetEntityProperties() {
        return $this->EntityProperties;
    }
    
    /**
     * {@inheritDoc}
     */
    final public function GetCollectionProperties() {
        return $this->CollectionProperties;
    }
    
    /**
     * {@inheritDoc}
     */
    protected abstract function ConstructEntity();
    
    /**
     * {@inheritDoc}
     */
    final public function HasIdentity($Entity) {
        foreach($this->Identity($Entity) as $Value) {
            if($Value === null) {
                return false;
            }
        }
        return true;
    }
    
    private $Identity = null;
    /**
     * {@inheritDoc}
     */
    final public function Identity($Entity = null) {
        if($this->Identity === null) {
            $this->Identity = new Identity($this);
        }
        
        $IdentityData = array();
        if($Entity !== null) {
            $this->VerifyEntity(__METHOD__, $Entity);
            foreach($this->IdentityProperties as $Identifier => $IdentityProperty) {
                $IdentityData[$Identifier] = $IdentityProperty->GetValue($Entity);
            }
        }
        
        return $this->Identity->Another($IdentityData);
    }
    
    /**
     * @var RevivalData
     */
    private $RevialData = null;
    /**
     * {@inheritDoc}
     */
    final public function RevivalData(array $RevivalData = array()) {
        if($this->RevialData === null) {
            $this->RevialData = new RevivalData($this);
        }
        return $this->RevialData->Another($RevivalData);
    }
    
    /**
     * @var PersistenceData
     */
    private $PersistenceData = null;
    /**
     * {@inheritDoc}
     */
    final public function PersistanceData(array $PersistanceData = array()) {
        if($this->PersistenceData === null) {
            $this->PersistenceData = new PersistenceData($this);
        }
        return $this->PersistenceData->Another($PersistanceData);
    }
    
    /**
     * @var DiscardenceData
     */
    private $DiscardenceData = null;
    /**
     * {@inheritDoc}
     */
    final public function DiscardenceData(array $DiscardenceData = array()) {
        if($this->DiscardenceData === null) {
            $this->DiscardenceData = new DiscardenceData($this);
        }
        return $this->DiscardenceData->Another($DiscardenceData);
    }
    
    /**
     * {@inheritDoc}
     */
    final public function Persist(UnitOfWork $UnitOfWork, $Entity) {
        $this->VerifyEntity(__METHOD__, $Entity);
        
        $PersistenceData = array();
        foreach($this->DataProperties as $Identifier => $DataProperty) {
            $PersistenceData[$Identifier] = $DataProperty->GetValue($Entity);
        }
        foreach($this->EntityProperties as $Identifier => $EntityProperty) {
            $PersistenceData[$Identifier] = $EntityProperty->Persist($UnitOfWork, $Entity);
        }
        foreach($this->CollectionProperties as $Identifier => $CollectionProperty) {
            $PersistenceData[$Identifier] = $CollectionProperty->Persist($UnitOfWork, $Entity);
        }
        
        return $this->PersistanceData($PersistenceData);
    }
    
    /**
     * {@inheritDoc}
     */
    final public function PersistRelationships(UnitOfWork $UnitOfWork, $Entity) {
        $this->VerifyEntity(__METHOD__, $Entity);
        
        $PersistenceData = array();
        foreach($this->EntityProperties as $Identifier => $EntityProperty) {
            $PersistenceData[$Identifier] = $EntityProperty->Persist($UnitOfWork, $Entity);
        }
        foreach($this->CollectionProperties as $Identifier => $CollectionProperty) {
            $PersistenceData[$Identifier] = $CollectionProperty->Persist($UnitOfWork, $Entity);
        }
        
        return $this->PersistanceData($PersistenceData);
    }
    
    /**
     * {@inheritDoc}
     */
    final public function Discard(UnitOfWork $UnitOfWork, $Entity) {
        $this->VerifyEntity(__METHOD__, $Entity);
        
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
        
        return $this->DiscardenceData($DiscardingData);
    }
    
    /**
     * {@inheritDoc}
     */
    final public function Apply(Domain $Domain, $Entity, PropertyData $PropertyData) {
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    final public function LoadEntity(Domain $Domain, RevivalData $RevivalData, $Entity) {
        $this->Apply($Domain, $Entity, $RevivalData);
    }
}

?>