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
     * @var EntityMap[] 
     */
    private $EntityMaps = array();
    
    public function __construct() {
        $Registrar = new Registrar(EntityMap::GetType());
        $this->RegisterEntityMaps($Registrar);
        foreach($Registrar->GetRegistered() as $EntityMap) {
            $this->AddEntityMap($EntityMap);
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
    final protected function AddEntityMap(EntityMap $EntityMap) {
        $EntityMap->InititalizeProperties($this);
        $this->EntityMaps[$EntityMap->GetEntityType()] = $EntityMap;
    }
    
    /**
     * @param string $EntityType The type of entity
     * @return bool
     */
    final public function HasEntityMap($EntityType) {
        return $this->GetMatchingEntityType($EntityType) !== null;
    }
    
    /**
     * @param string $EntityType The type of entity that the entity map represents
     * @return EntityMap|null The entity map or null if it is not registered
     */
    final public function GetEntityMap($EntityType) {
        return $this->HasEntityMap($EntityType) ?
                $this->EntityMaps[$this->GetMatchingEntityType($EntityType)] : null;
    }
    
    /**
     * Verifies that an entity is valid in this domain.
     * 
     * @param object $Entity The entity to verify
     * @return EntityMap The matching entity map
     * @throws \Storm\Core\Exceptions\UnmappedEntityException
     */
    private function VerifyEntity($Entity, &$MatchedEntityType = null) {
        $MatchedEntityType = $this->GetMatchingEntityType($Entity);
        if($MatchedEntityType !== null) {
            return $this->EntityMaps[$MatchedEntityType];
        }
        else {
            throw new \Storm\Core\Exceptions\UnmappedEntityException(get_class($Entity));
        }
    }
    /**
     * @param object|string $EntityOrEntityType The entity to check or entity type
     * @return string|null The matching entity type
     */
    final public function GetMatchingEntityType($EntityOrEntityType) {
        $EntityType = is_object($EntityOrEntityType) ? get_class($EntityOrEntityType) : $EntityOrEntityType;
        if(isset($this->EntityMaps[$EntityType])) {
            return $EntityType;
        }
        $EntityTypes = array_reverse(array_merge([$EntityType], array_values(class_parents($EntityOrEntityType, false))));
        foreach($EntityTypes as $EntityType) {
            if(isset($this->EntityMaps[$EntityType])) {
                return $EntityType;
            }
        }           
        return null;
    }
    
    /**
     * @param object $Entity The entity to check
     * @return boolean Whether or not the entity has an identity
     */
    final public function HasIdentity($Entity) {
        return $this->VerifyEntity($Entity)->HasIdentity($Entity);
    }
    
    /**
     * Determines is two entities share the same identity.
     * 
     * @param object $Entity
     * @param object $OtherEntity
     * @return boolean Whether or not that the entities have the same identity
     */
    final public function DoShareIdentity($Entity, $OtherEntity) {
        $EntityMap = $this->VerifyEntity($Entity);
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
        return $this->VerifyEntity($Entity)->Identity($Entity);
    }
    
    /**
     * Applies the supplied property data to the supplied entity instance.
     * 
     * @param object $Entity The entity to apply the property data
     * @param array $PropertyData The property data apply
     * @return void
     */
    final public function Apply($Entity, array $PropertyData) {
        return $this->VerifyEntity($Entity)->Apply($this, $Entity, $PropertyData);
    }
    
    /**
     * Constructs a discarded non-identifying relationship between the two supplied entities.
     * 
     * @param object $Entity 
     * @param object $RelatedEntity
     * @return DiscardedRelationship The discarded relationship
     */
    final public function DiscardedRelationship($Entity, $RelatedEntity) {
        $EntityType = null;
        $RelatedEntityType = null;
        $ParentIdentity = $this->VerifyEntity($Entity, $EntityType)->Identity($Entity);
        $RelatedIdentity = $this->VerifyEntity($RelatedEntity, $RelatedEntityType)->Identity($RelatedEntity);
        
        return new DiscardedRelationship(false, $EntityType, $RelatedEntityType, $ParentIdentity, $RelatedIdentity);
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
        $EntityType = null;
        $RelatedEntityType = null;
        $ParentIdentity = $this->VerifyEntity($ParentEntity, $EntityType)->Identity($ParentEntity);
        $ChildIdentity = $this->VerifyEntity($ChildEntity, $RelatedEntityType)->Discard($UnitOfWork, $ChildEntity)->GetIdentity();
        
        return new DiscardedRelationship(true, $EntityType, $RelatedEntityType, $ParentIdentity, $ChildIdentity);
    }
    
    /**
     * Constructs a discarded non-identifying relationship between the two supplied entities.
     * 
     * @param object $Entity
     * @param object $RelatedEntity
     * @return PersistedRelationship The persisted relationship
     */
    final public function PersistedRelationship($ParentEntity, $RelatedEntity) {
        $EntityType = null;
        $RelatedEntityType = null;
        $ParentIdentity = $this->VerifyEntity($ParentEntity, $EntityType)->Identity($ParentEntity);
        $RelatedIdentity = $this->VerifyEntity($RelatedEntity, $RelatedEntityType)->Identity($RelatedEntity);
        
        return new PersistedRelationship($EntityType, $RelatedEntityType, $ParentIdentity, $RelatedIdentity);
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
        $EntityType = null;
        $RelatedEntityType = $this->GetMatchingEntityType($ChildEntity);
        $ParentIdentity = $this->VerifyEntity($ParentEntity, $EntityType)->Identity($ParentEntity);
        $RelatedPersistenceData = $this->VerifyEntity($ChildEntity, $RelatedEntityType)->Persist($UnitOfWork, $ChildEntity);
        $UnitOfWork->RequestIdentity($RelatedPersistenceData, $ChildEntity);
        
        return new PersistedRelationship($EntityType, $RelatedEntityType, $ParentIdentity, null, $RelatedPersistenceData);
    }
    
    /**
     * Persists an entity relationships to the supplied unit of work and returns the entity's
     * persistence data.
     * 
     * @param UnitOfWork $UnitOfWork The unit of work to persist to
     * @param object $Entity The entity to persist
     * @return array The persistence data of the entity
     */
    final public function Persist(UnitOfWork $UnitOfWork, $Entity) {
        return $this->VerifyEntity($Entity)->Persist($UnitOfWork, $Entity);
    }
    
    /**
     * Discard an entity relationships to the supplied unit of work and returns the entity's
     * discardence data.
     * 
     * @param UnitOfWork $UnitOfWork The unit of work to discard to
     * @param object $Entity The entity to discard
     * @return array The discardence data of the entity
     */
    final public function Discard(UnitOfWork $UnitOfWork, $Entity) {
        return $this->VerifyEntity($Entity)->Discard($UnitOfWork, $Entity);
    }
    
    /**
     * Revives an array of entities from the supplied array of revival data.
     * 
     * @param string $EntityType The type of entities to revive
     * @param array[] $RevivalData The array of revival data
     * @return object[] The revived entities
     */
    final public function ReviveEntities($EntityType, array $RevivalData) {
        if(count($RevivalData) === 0) {
            return array();
        }
        $EntityMap = $this->GetEntityMap($EntityType);
        
        return $EntityMap->ReviveEntities($this, $RevivalData);
    }
    
    /**
     * Loads an entity instance with the supplied revival data.
     * 
     * @param array $RevivalData The revival data to load the entity with
     * @param object $Entity The entity to load
     * @return void
     */
    final public function LoadEntity(array $RevivalData, $Entity) {
        $EntityMap = $this->EntityMaps[$RevivalData->GetEntityType()];
        
        $EntityMap->LoadEntity($this, $RevivalData, $Entity);
    }
    
    /**
     * Constructs a unit of work instance containing the supplied operations to commit.
     * 
     * @param object[] $EntitiesToPersist
     * @param IProcedure[] $ProceduresToExecute
     * @param object[] $EntitiesToDiscard
     * @param ICriterion[] $CriterionToDiscard
     * @return UnitOfWork The constructed unit of work
     */
    final public function BuildUnitOfWork(
            array $EntitiesToPersist = array(),
            array $ProceduresToExecute = array(),
            array $EntitiesToDiscard = array(), 
            array $CriterionToDiscard = array()) {
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
        foreach($CriterionToDiscard as $Criterion) {
            $UnitOfWork->DiscardWhere($Criterion);
        }
        
        return $UnitOfWork;
    }
    
}

?>