<?php

namespace Storm\Api\Base;

use \Storm\Api\IConfiguration;
use \Storm\Core\Object;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Drivers\Base;
use \Storm\Drivers\Fluent\Object\Functional;

/**
 * The Repository provides the clean api for querying on a specific
 * type of entity.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Repository {
    /**
     * The DomainDatabaseMap to query.
     * 
     * @var DomainDatabaseMap
     */
    protected $DomainDatabaseMap;
    
    /**
     * @var FunctionToASTConverter
     */
    protected $FunctionToASTConverter;
    
    /**
     * The type of entity represented by this repository.
     * 
     * @var string
     */
    protected $EntityType;
    
    /**
     * The EntityMap for this repository.
     * 
     * @var Object\IEntityMap
     */
    protected $EntityMap;
    
    /**
     * The properties representing the identity of the entity.
     * 
     * @var IProperty[] The prop
     */
    protected $IdentityProperties;
    
    /**
     * Whether or not to save on every change.
     * 
     * @var boolean 
     */
    private $AutoSave = false;
    
    /**
     * Entities that are awaiting persistence
     * 
     * @var array 
     */
    private $PersistedQueue = array();
    
    /**
     * Procedures that are awaiting execution
     * 
     * @var array 
     */
    private $ExecutionQueue = array();
    
    /**
     * Entities that are awaiting to be discarded
     * 
     * @var array 
     */
    private $DiscardedQueue = array();
    
    /**
     * Criteria of entities to discard
     * 
     * @var array 
     */
    private $DiscardedCriterionQueue = array();    
    
    /**
     * The cache to use as the identity map for the repository
     * 
     * @var IdentityMap
     */
    private $IdentityMap;    
    
    public function __construct(
            DomainDatabaseMap $DomainDatabaseMap,
            FunctionToASTConverter $FunctionToASTConverter,
            $EntityType) {
        $this->DomainDatabaseMap = $DomainDatabaseMap;
        $this->EntityMap = $this->DomainDatabaseMap->GetDomain()->GetEntityMap($EntityType);
        $this->IdentityProperties = $this->EntityMap->GetIdentityProperties();
        $this->FunctionToASTConverter = $FunctionToASTConverter;
        $this->EntityType = $EntityType;
        $this->IdentityMap = new IdentityMap($this->EntityMap, new \Storm\Utilities\Cache\MemoryCache());
    }
    
    /**
     * Gets the identity map used for this repository.
     * 
     * @return IdentityMap
     */
    public function GetIdentityMap() {
        return $this->IdentityMap;
    }

    /**
     * Verifies an entity to be valid for use in this repository.
     * 
     * @param string $Method __METHOD__
     * @param object $Entity The entity to verify
     * @throws Object\TypeMismatchException
     */
    final protected function VerifyEntity($Method, $Entity) {
        if(!($Entity instanceof $this->EntityType)) {
            throw new Object\TypeMismatchException('Call to method %s with invalid entity: %s expected, %s given', 
                    $Method, 
                    $this->EntityType,
                    \Storm\Core\Utilities::GetTypeOrClass($Entity));
        }
    }
    
    /**
     * Set whether or not to automatically commit every change.
     * 
     * @param boolean $AutoSave
     * @return void
     */
    final public function SetAutoSave($AutoSave) {
        $this->AutoSave = $AutoSave;
    }
    
    /**
     * Quick access to a new RequestBuilder instance.
     * 
     * @return Fluent\RequestBuilder 
     */
    final public function Request() {
        return new Fluent\RequestBuilder($this->EntityMap, $this->FunctionToASTConverter);
    }
    
    /**
     * Quick access to a new ProcedureBuilder instance.
     * 
     * @return Fluent\ProcedureBuilder
     */
    final function Procedure(callable $ProcedureClosure) {
        return new Fluent\ProcedureBuilder($this->EntityMap, $this->FunctionToASTConverter, $ProcedureClosure);
    }
    
    /**
     * Quick access to a new CriterionBuilder instance.
     * 
     * @return Fluent\CriterionBuilder
     */
    final function Criterion() {
        return new Fluent\CriterionBuilder($this->EntityMap, $this->FunctionToASTConverter);
    }
    
    /**
     * Load a request directly from the builder instance.
     * 
     * @param Fluent\RequestBuilder $RequestBuilder The builder representing the request to load
     * @return object|null|array The returned results
     */
    public function Load(Fluent\RequestBuilder $RequestBuilder) {
        return $this->LoadRequest($RequestBuilder->BuildRequest());
    }
    
    /**
     * Load entities specified by a request instance.
     * 
     * @param Object\IRequest $Request The request to load
     * @return object|null|array
     * @throws Object\TypeMismatchException
     */
    public function LoadRequest(Object\IRequest $Request) {
        if($Request->GetEntityType() !== $this->EntityType) {
            throw new Object\TypeMismatchException('The supplied request is of type %s, expecting: %s', $Request->GetEntityType(), $this->EntityType);
        }
        $Entities = $this->DomainDatabaseMap->Load($Request);
        
        if(is_array($Entities)) {
            $this->IdentityMap->CacheEntities($Entities);
        }
        else if ($Entities instanceof $this->EntityType) {
            $this->IdentityMap->CacheEntity($Entities);
        }
        
        return $Entities;
    }
    
    /**
     * Loads an entity from given identity values or null if entity does not exist.
     * 
     * @param mixed ... The identity value(s)  
     * @return object|null The returned entity or null
     * @throws \Storm\Core\StormException
     */
    public function LoadById($_) {
        $IdentityValues = func_get_args();
        if(count($IdentityValues) !== count($this->IdentityProperties)) {
            throw new \Storm\Core\StormException(
                    'The supplied amount of parameters does not match the number of '
                    . 'identity properties for %s, expecting %d: %d were supplied', 
                    $this->EntityType, count($this->IdentityProperties), count($IdentityValues));
        }
        
        $Identity = $this->EntityMap->Identity();
        $Count = 0;
        foreach($this->IdentityProperties as $IdentityProperty) {
            $Identity[$IdentityProperty] = $IdentityValues[$Count];
            $Count++;
        }
        
        return $this->LoadByIdentity($Identity);
    }
    
    /**
     * Loads an entity from an identity instance.
     * 
     * @param Object\Identity $Identity The identity of the entity
     * @return object|null
     */
    protected function LoadByIdentity(Object\Identity $Identity) {
        $CachedEntity = $this->IdentityMap->GetFromCache($Identity);
        if($CachedEntity instanceof $this->EntityType) {
            return $CachedEntity;
        }
        
        $Entity = $this->DomainDatabaseMap->Load(
                new Base\Object\Request(
                        $this->EntityType, 
                        $this->EntityMap->GetProperties(), 
                        true, 
                        new Base\Object\Criteria\MatchesCriterion($Identity)));
        
        if($Entity instanceof $this->EntityType) {
            $this->IdentityMap->CacheEntity($Entity, $Identity);
        }
        
        return $Entity;
    }
    
    /**
     * Adds an entity to the persistence queue. 
     * If AutoSave is enabled, the action will be commited.
     * 
     * @param object $Entity The entity to persist
     * @return void
     */
    public function Persist($Entity) {
        $this->VerifyEntity(__METHOD__, $Entity);
        $this->IdentityMap->CacheEntity($Entity);
        
        $this->PersistedQueue[] = $Entity;
        $this->AutoSave();
    }
    
    /**
     * Adds an array of entities to the persistence queue. 
     * If AutoSave is enabled, the action will be commited.
     * 
     * @param array $Entities The entities to persist
     * @return void
     */
    public function PersistAll(array $Entities) {
        $this->IdentityMap->CacheEntities($Entities);
        
        $this->PersistedQueue = array_merge($this->PersistedQueue, $Entities);
        $this->AutoSave();
    }
    
    /**
     * Adds a procedure to the execution queue directly from the builder. 
     * If AutoSave is enabled, the action will be commited.
     * 
     * @param Fluent\ProcedureBuilder $ProcedureBuilder The procedure to build and execute
     * @return void
     */
    public function Execute(Fluent\ProcedureBuilder $ProcedureBuilder) {
        $this->ExecuteProcedure($ProcedureBuilder->BuildProcedure());
    }
    
    /**
     * Adds a procedure to the execution queue. 
     * If AutoSave is enabled, the action will be commited.
     * 
     * @param Fluent\ProcedureBuilder $ProcedureBuilder The procedure to execute
     * @return void
     */
    public function ExecuteProcedure(Object\IProcedure $Procedure) {
        if($Procedure->GetEntityType() !== $this->EntityType) {
            throw new Object\TypeMismatchException('The supplied procedure is of type %s, expecting: %s', $Procedure->GetEntityType(), $this->EntityType);
        }
        $this->ExecutionQueue[] = $Procedure;
        $this->AutoSave();
    }
    
    /**
     * Adds an entity or criterion to the discardence queue. 
     * If AutoSave is enabled, the action will be commited.
     * 
     * @param object|Fluent\CriterionBuilder|Object\ICriterion $EntityOrCriterion The entity or criterion to discard
     * @return void
     */
    public function Discard($EntityOrCriterion) {
        if($EntityOrCriterion instanceof Fluent\CriterionBuilder) {
            $this->DiscardedCriterionQueue[] = $EntityOrCriterion->BuildCriterion();
        }
        else if($EntityOrCriterion instanceof Object\ICriterion) {
            $this->DiscardedCriterionQueue[] = $EntityOrCriterion;
        }
        else {
            $this->VerifyEntity(__METHOD__, $EntityOrCriterion);
            $this->IdentityMap->RemoveFromCache($EntityOrCriterion);
            $this->DiscardedQueue[] = $EntityOrCriterion;
        }
        
        $this->AutoSave();
    }
    
    /**
     * Adds an array of entities to the discardence queue. 
     * If AutoSave is enabled, the action will be commited.
     * 
     * @param array $Entities The entities to discard
     * @return void
     */
    public function DiscardAll(array $Entities) {
        $this->IdentityMap->RemoveAllFromCache($Entities);
        $this->DiscardedQueue = array_merge($this->DiscardedQueue, $Entities);
        $this->AutoSave();
    }
    
    /**
     * Commits changes if AutoSave is enabled.
     * 
     * @return void
     */
    private function AutoSave() {
        if($this->AutoSave) {
            $this->SaveChanges();
        }
    }
    
    /**
     * Commits all specified changes to the underlying DomainDatabaseMap.
     * 
     * @return void
     */
    public function SaveChanges() {
        if(count($this->PersistedQueue) === 0 && 
                count($this->ExecutionQueue) === 0 &&
                count($this->DiscardedQueue) === 0 &&
                count($this->DiscardedCriterionQueue) === 0) {
            return;
        }
        
        $this->DomainDatabaseMap->Commit(
                $this->PersistedQueue, 
                $this->ExecutionQueue, 
                $this->DiscardedQueue, 
                $this->DiscardedCriterionQueue);
        
        $this->ClearChanges();
    }
    
    /**
     * Gets the pending changes.
     * 
     * @return array An array containing all the operations queues
     */
    final public function GetChanges() {
        return [$this->PersistedQueue, 
                $this->ExecutionQueue, 
                $this->DiscardedQueue, 
                $this->DiscardedCriterionQueue];
    }
    
    /**
     * Clears all the pending changes awaiting to be 
     * commited to underlying DomainDatabaseMap.
     * 
     * @return void
     */
    final public function ClearChanges() {
        $this->PersistedQueue = array();
        $this->ExecutionQueue = array();
        $this->DiscardedQueue = array();
        $this->DiscardedCriterionQueue = array();
    }
}

?>