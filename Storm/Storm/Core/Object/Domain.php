<?php

namespace Storm\Core\Object;

use \Storm\Core\Containers\Registrar;
use \Storm\Core\Containers\Map;

/**
 * This is the base class representing the domain of the application.
 * The domain represents a group of entities, their properties and relationships.
 * A single entity is represented through an entity map.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class Domain {
    use \Storm\Core\Helpers\Type;
    
    /**
     * The registered entity maps.
     * 
     * @var IEntityMap[] 
     */
    private $EntityMaps = [];
    
    public function __construct() {
        $Registrar = new Registrar(IEntityMap::IEntityMapType);
        $this->RegisterEntityMaps($Registrar);
        foreach($Registrar->GetRegistered() as $EntityMap) {
            $this->AddEntityMap($EntityMap);
        }
        foreach ($this->EntityMaps as $EntityMap) {
            $EntityMap->InitializeRelationshipProperties($this);
        }
    }
    
    /**
     * The method to register the entity maps within this domain.
     * 
     * @param Registrar $Registrar
     * @return void
     */
    protected abstract function RegisterEntityMaps(Registrar $Registrar);
    
    /**
     * Adds an entity map to the domain.
     * 
     * @param EntityMap $EntityMap The entity map to add.
     * @return void
     */
    final protected function AddEntityMap(IEntityMap $EntityMap) {
        $EntityMap->InititalizeProperties($this);
        $this->EntityMaps[$EntityMap->GetEntityType()] = $EntityMap;
    }
    
    /**
     * @param string $EntityType The type of entity
     * @return bool
     */
    final public function HasEntityMap($EntityType) {
        return isset($this->EntityMaps[$EntityType]);
    }
    
    /**
     * @return IEntityMap[] The registered entity maps
     */
    final public function GetEntityMaps() {
        return $this->EntityMaps;
    }
    
    /**
     * @param string $EntityType The type of entity that the entity map represents
     * @return IEntityMap|null The entity map or null if it is not registered
     */
    final public function GetEntityMap($EntityType) {
        return $this->HasEntityMap($EntityType) ? $this->EntityMaps[$EntityType] : null;
    }
    
    /**
     * Verifies that an entity is valid in this domain.
     * 
     * @param string $Method __METHOD__
     * @param object $Entity The entity to verify
     * @return IEntityMap The matching entity map
     * @throws UnmappedEntityException
     */
    private function VerifyEntity($Method, $Entity) {
        $EntityTypes = array_reverse(array_merge([get_class($Entity)], array_values(class_parents($Entity, false))));
        foreach($EntityTypes as $EntityType) {
            if(isset($this->EntityMaps[$EntityType])) {
                return $this->EntityMaps[$EntityType];
            }
        }           
        throw new UnmappedEntityException(
                'Call to %s with supplied entity of type %s has not been mapped',
                $Method, 
                get_class($Entity));
    }
    
    /**
     * @param object $Entity The entity to check
     * @return boolean Whether or not the entity has an identity
     */
    final public function HasIdentity($Entity) {
        return $this->VerifyEntity(__METHOD__, $Entity)->HasIdentity($Entity);
    }
    
    /**
     * Determines is two entities share the same identity.
     * 
     * @param object $Entity
     * @param object $OtherEntity
     * @return boolean Whether or not that the entities have the same identity
     */
    final public function DoShareIdentity($Entity, $OtherEntity) {
        $EntityMap = $this->VerifyEntity(__METHOD__, $Entity);
        $EntityType = $EntityMap->GetEntityType();
        if(!($OtherEntity instanceof $EntityType)) {
            return false;
        }
        else {
            return $EntityMap->Identity($Entity)->Matches($EntityMap->Identity($OtherEntity));
        }
    }
    
    /**
     * Gets the identity from the supplied entity.
     * 
     * @param object $Entity 
     * @return Identity 
     */
    final public function Identity($Entity) {
        return $this->VerifyEntity(__METHOD__, $Entity)->Identity($Entity);
    }
    
    /**
     * Constructs a discarded non-identifying relationship between the two supplied entities.
     * 
     * @param object $Entity 
     * @param object $RelatedEntity
     * @return DiscardedRelationship The discarded relationship
     */
    final public function DiscardedRelationship($Entity, $RelatedEntity) {
        $ParentIdentity = $this->VerifyEntity(__METHOD__, $Entity)->Identity($Entity);
        $RelatedIdentity = $this->VerifyEntity(__METHOD__, $RelatedEntity)->Identity($RelatedEntity);
        
        return new DiscardedRelationship(false, $ParentIdentity, $RelatedIdentity);
    }
    
    /**
     * Constructs a discarded identifying relationship between the parent and child entity.
     * NOTE: The child entity relationships will be discarded in the supplied unit of work.
     * 
     * @param object $Entity 
     * @param object $ChildEntity
     * @param UnitOfWork $UnitOfWork 
     * @return DiscardedRelationship The discarded relationship
     */
    final public function DiscardedIdentifyingRelationship($ParentEntity, $ChildEntity, UnitOfWork $UnitOfWork) {
        $ParentIdentity = $this->VerifyEntity(__METHOD__, $ParentEntity)->Identity($ParentEntity);
        $ChildIdentity = $this->VerifyEntity(__METHOD__, $ChildEntity)->Discard($UnitOfWork, $ChildEntity)->GetIdentity();
        
        return new DiscardedRelationship(true, $ParentIdentity, $ChildIdentity);
    }
    
    /**
     * Constructs a discarded non-identifying relationship between the two supplied entities.
     * 
     * @param object $Entity
     * @param object $RelatedEntity
     * @return PersistedRelationship The persisted relationship
     */
    final public function PersistedRelationship($ParentEntity, $RelatedEntity) {
        $ParentIdentity = $this->VerifyEntity(__METHOD__, $ParentEntity)->Identity($ParentEntity);
        $RelatedIdentity = $this->VerifyEntity(__METHOD__, $RelatedEntity)->Identity($RelatedEntity);
        
        return new PersistedRelationship($ParentIdentity, $RelatedIdentity);
    }
    
    /**
     * Constructs a persisted identifying relationship between the parent and child entity.
     * NOTE: The child entity relationships will be persisted in the supplied unit of work.
     * 
     * @param object $Entity 
     * @param object $ChildEntity
     * @param UnitOfWork $UnitOfWork 
     * @return PersistedRelationship The persisted relationship
     */
    final public function PersistedIdentifyingRelationship($ParentEntity, $ChildEntity, UnitOfWork $UnitOfWork) {
        $ParentIdentity = $this->VerifyEntity(__METHOD__, $ParentEntity)->Identity($ParentEntity);
        $RelatedPersistenceData = $this->VerifyEntity(__METHOD__, $ChildEntity)->Persist($UnitOfWork, $ChildEntity);
        
        return new PersistedRelationship($ParentIdentity, null, $RelatedPersistenceData);
    }
    
    /**
     * Persists an entity relationships to the supplied unit of work and returns the entity's
     * persistence data.
     * 
     * @param UnitOfWork $UnitOfWork The unit of work to persist to
     * @param object $Entity The entity to persist
     * @return PersistenceData The persistence data of the entity
     */
    final public function Persist(UnitOfWork $UnitOfWork, $Entity) {
        return $this->VerifyEntity(__METHOD__, $Entity)->Persist($UnitOfWork, $Entity);
    }
    
    /**
     * Persists an entity relationships to the supplied unit of work.
     * 
     * @param UnitOfWork $UnitOfWork The unit of work to persist to
     * @param object $Entity The entity to persist
     * @return void The persistence data of the entity
     */
    final public function PersistRelationships(UnitOfWork $UnitOfWork, $Entity) {
        $this->VerifyEntity(__METHOD__, $Entity)->PersistRelationships($UnitOfWork, $Entity);
    }
    
    /**
     * Discard an entity relationships to the supplied unit of work and returns the entity's
     * discardence data.
     * 
     * @param UnitOfWork $UnitOfWork The unit of work to discard to
     * @param object $Entity The entity to discard
     * @return DiscardenceData The discardence data of the entity
     */
    final public function Discard(UnitOfWork $UnitOfWork, $Entity) {
        return $this->VerifyEntity(__METHOD__, $Entity)->Discard($UnitOfWork, $Entity);
    }
    
    /**
     * Applies the supplied property data to the supplied entity instance.
     * 
     * @param object $Entity The entity to apply the property data
     * @param PropertyData $PropertyData The property data apply
     * @return void
     */
    final public function Apply($Entity, PropertyData $PropertyData) {
        return $this->VerifyEntity(__METHOD__, $Entity)->Apply($Entity, $PropertyData);
    }
    
    /**
     * Revives an array of entities from the supplied array of revival data.
     * 
     * @param string $EntityType The type of entities to revive
     * @param RevivalData[] $RevivalData The array of revival data
     * @return object[] The revived entities
     */
    final public function ReviveEntities($EntityType, array $RevivalData) {
        if(count($RevivalData) === 0) {
            return [];
        }
        $EntityMap = $this->GetEntityMap($EntityType);
        
        return $EntityMap->ReviveEntities($RevivalData);
    }
    
    /**
     * Loads an entity instance with the supplied revival data.
     * 
     * @param RevivalData $RevivalData The revival data to load the entity with
     * @param object $Entity The entity to load
     * @return void
     */
    final public function LoadEntity(RevivalData $RevivalData, $Entity) {
        $EntityMap = $this->EntityMaps[$RevivalData->GetEntityType()];
        
        $EntityMap->LoadEntity($RevivalData, $Entity);
    }
    
    /**
     * Loads an array of entities with the supplied revival data.
     * 
     * @param RevivalData $RevivalData The revival data to load the entity with
     * @param array $Entities The entities to load
     * @return void
     */
    final public function LoadEntities(RevivalData $RevivalData, array $Entities) {
        $EntityMap = $this->EntityMaps[$RevivalData->GetEntityType()];
        
        foreach($Entities as $Entity) {
            $EntityMap->LoadEntity($RevivalData, $Entity);
        }
    }
    
    /**
     * Constructs a unit of work instance containing the supplied operations to commit.
     * 
     * @param object[] $EntitiesToPersist
     * @param IProcedure[] $ProceduresToExecute
     * @param object[] $EntitiesToDiscard
     * @param ICriteria[] $CriteriaToDiscard
     * @return UnitOfWork The constructed unit of work
     */
    final public function BuildUnitOfWork(
            array $EntitiesToPersist = [],
            array $ProceduresToExecute = [],
            array $EntitiesToDiscard = [], 
            array $CriteriaToDiscard = []) {
        $UnitOfWork = new UnitOfWork($this);
        
        foreach($EntitiesToPersist as $Entity) {
            $UnitOfWork->Persist($Entity);
        }
        foreach($ProceduresToExecute as $Procedure) {
            $UnitOfWork->Execute($Procedure);
        }
        foreach($EntitiesToDiscard as $Entity) {
            $UnitOfWork->Discard($Entity);
        }
        foreach($CriteriaToDiscard as $Criteria) {
            $UnitOfWork->DiscardWhere($Criteria);
        }
        
        return $UnitOfWork;
    }
    
}

?>