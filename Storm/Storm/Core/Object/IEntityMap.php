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
interface IEntityMap {
    const IEntityMapType = __CLASS__;
    
    /**
     * Initializes the properties of the entity within the context of the parent domain.
     * 
     * @param Domain $Domain The parent domain
     * @return void
     */
    public function InititalizeProperties(Domain $Domain);
    
    /**
     * Whether or not this map contains a property withthe supplied identifier
     * 
     * @param string $Identifier The property identifier
     * @return boolean
     */
    public function HasProperty($Identifier);
    
    /**
     * Whether or not this map contains a property with the supplied identifier
     * 
     * @param string $Identifier The property identifier
     * @return boolean
     */
    public function HasIdentityProperty($Identifier);
    
    /**
     * Whether or not this map contains a relationship property with the supplied identifier
     * 
     * @param string $Identifier The property identifier
     * @return void
     */
    public function HasRelationshipProperty($Identifier);
    
    /**
     * Gets a property by its identifier
     * 
     * @param string $Identifier The property identifier
     * @return IProperty|null The matched property
     */
    public function GetProperty($Identifier);
    
    /**
     * @return IProperty[]
     */
    public function GetIdentityProperties();
    
    /**
     * @return string
     */
    public function GetEntityType();
    
    /**
     * Whether or not this and the supplied entity map represent the same entity.
     * 
     * @param IEntityMap $OtherEntityMap Another entity map
     * @return boolean
     */
    public function Is(IEntityMap $OtherEntityMap);
    
    /**
     * @return IProperty[]
     */
    public function GetProperties();
    
    /**
     * @return IDataProperty[]
     */
    public function GetDataProperties();
    
    /**
     * @return IRelationshipProperty[]
     */
    public function GetRelationshipProperties();
    
    /**
     * @return IEntityProperty[]
     */
    public function GetEntityProperties();
    
    /**
     * @return ICollectionProperty[]
     */
    public function GetCollectionProperties();
    
    /**
     * Whether or not the entity has a full identity.
     * 
     * @param object $Entity The entity to check
     * @return boolean
     */
    public function HasIdentity($Entity);
    /**
     * If the entity is null returns a new blank identity otherwise returns the identity
     * of the supplied entity.
     * 
     * @param object|null $Entity
     * @return Identity The entity's identity
     */
    public function Identity($Entity = null);
    
    /**
     * @return RevivalData
     */
    public function RevivalData(array $RevivalData = []);
    
    /**
     * @return PersistenceData
     */
    public function PersistanceData(array $PersistanceData = []);
    
    /**
     * @return DiscardenceData
     */
    public function DiscardenceData(array $DiscardenceData = []);
    
    /**
     * Persists an entity's relationships to the supplied unit of work and returns the persistence data.
     * 
     * @param UnitOfWork $UnitOfWork The unit of work to persist to
     * @param object $Entity The entity to persist
     * @return PersistenceData The persistence data of the entity
     */
    public function Persist(UnitOfWork $UnitOfWork, $Entity);
    
    /**
     * Persists an entity's relationships to the supplied unit of work.
     * 
     * @param UnitOfWork $UnitOfWork The unit of work to persist to
     * @param object $Entity The entity to persist
     * @return PersistenceData The persistence data of the entity
     */
    public function PersistRelationships(UnitOfWork $UnitOfWork, $Entity);
    
    /**
     * Discards an entity's relationships to the supplied unit of work and returns the discardence data.
     * 
     * @param UnitOfWork $UnitOfWork The unit of work to discard from
     * @param object $Entity The entity to discard
     * @return DiscardenceData The discardence data of the entity
     */
    public function Discard(UnitOfWork $UnitOfWork, $Entity);
    
    /**
     * Applies the supplied property data to the supplied entity instance.
     * 
     * @param Domain $Domain The object domain to revive in entity in.
     * @param object $Entity The entity to apply the property data
     * @param PropertyData $PropertyData The property data apply
     * @return void
     */
    public function Apply(Domain $Domain, $Entity, PropertyData $PropertyData);
    
    /**
     * Revives an array of entities from the supplied array of revival data.
     * 
     * @param string $EntityType The type of entities to revive
     * @param RevivalData[] $RevivalDataArray The array of revival data
     * @return object[] The revived entities
     */
    public function ReviveEntities(Domain $Domain, array $RevivalDataArray);
    
    /**
     * Loads an entity instance with the supplied revival data.
     * 
     * @param RevivalData $RevivalData The revival data to load the entity with
     * @param object $Entity The entity to load
     * @return void
     */
    public function LoadEntity(Domain $Domain, RevivalData $RevivalData, $Entity);
}

?>