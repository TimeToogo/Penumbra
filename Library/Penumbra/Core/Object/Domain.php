<?php

namespace Penumbra\Core\Object;

use \Penumbra\Core\Containers\Registrar;
use \Penumbra\Core\Containers\Map;

/**
 * This is the base class representing the domain of the application.
 * The domain represents a group of entities, their properties and relationships.
 * A single entity is represented through an entity map.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class Domain {
    use \Penumbra\Core\Helpers\Type;
    
    /**
     * The registered entity maps.
     * 
     * @var IEntityMap[] 
     */
    private $EntityMaps = [];
    
    public function __construct() {
    }
    
    final public function InitializeEntityMaps() {
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
        return $this->GetEntityMap($EntityType) !== null;
    }
    
    /**
     * @return IEntityMap[] The registered entity maps
     */
    final public function GetEntityMaps() {
        return $this->EntityMaps;
    }
    
    /**
     * @param string $EntityType The type of entity that the entity map represents
     * @return IEntityMap|null The entity map or null if none is not registered
     */
    final public function GetEntityMap($EntityType) {        
        if(isset($this->EntityMaps[$EntityType])) {
            return $this->EntityMaps[$EntityType];
        }
        else {
            $ParentType = get_parent_class($EntityType);
            if($ParentType === false) {
                return null;
            }
            else {
                return $this->GetEntityMap($ParentType);
            }
        }
    }
    
    /**
     * @param string $EntityType The type of entity that the entity map represents
     * @return IEntityMap The entity map
     * @throws UnmappedEntityException
     */
    final public function VerifyEntityMap($EntityType) {
        $EntityMap = $this->GetEntityMap($EntityType);
        if($EntityMap === null) {
            throw new UnmappedEntityException(
                    'Cannot get entity map: %s does not have a registered entity map for type %s',
                    get_class($this), 
                    $EntityType);
        }
        return $EntityMap;
    }
    
    /**
     * Verifies that an entity is valid in this domain.
     * 
     * @param object $Entity The entity to verify
     * @return IEntityMap The matching entity map
     */
    final public function VerifyEntity($Entity) {
        return $this->VerifyEntityMap(get_class($Entity));
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
     * Persists an entity relationships to the supplied unit of work and returns the entity's
     * persistence data.
     * 
     * @param UnitOfWork $UnitOfWork The unit of work to persist to
     * @param object $Entity The entity to persist
     * @return PersistenceData The persistence data of the entity
     */
    final public function Persist(UnitOfWork $UnitOfWork, $Entity) {
        return $this->VerifyEntity($Entity)->Persist($UnitOfWork, $Entity);
    }
    
    /**
     * Persists an entity relationships to the supplied unit of work.
     * 
     * @param UnitOfWork $UnitOfWork The unit of work to persist to
     * @param object $Entity The entity to persist
     * @return void The persistence data of the entity
     */
    final public function PersistRelationships(UnitOfWork $UnitOfWork, $Entity) {
        $this->VerifyEntity($Entity)->PersistRelationships($UnitOfWork, $Entity);
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
        return $this->VerifyEntity($Entity)->Discard($UnitOfWork, $Entity);
    }
    
    /**
     * Applies the supplied property data to the supplied entity instance.
     * 
     * @param object $Entity The entity to apply the property data
     * @param PropertyData $PropertyData The property data apply
     * @return void
     */
    final public function Apply($Entity, PropertyData $PropertyData) {
        return $this->VerifyEntity($Entity)->Apply($Entity, $PropertyData);
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
            \Traversable $EntitiesToPersist,
            \Traversable $ProceduresToExecute,
            \Traversable $EntitiesToDiscard, 
            \Traversable $CriteriaToDiscard) {
        $UnitOfWork = new UnitOfWork($this);
        
        foreach($EntitiesToPersist as $Entity) {
            $UnitOfWork->PersistRoot($Entity);
        }
        foreach($ProceduresToExecute as $Procedure) {
            $UnitOfWork->Execute($Procedure);
        }
        foreach($EntitiesToDiscard as $Entity) {
            $UnitOfWork->Discard($Entity);
        }
        foreach($CriteriaToDiscard as $Criteria) {
            $UnitOfWork->DiscardBy($Criteria);
        }
        
        return $UnitOfWork;
    }
    
}

?>