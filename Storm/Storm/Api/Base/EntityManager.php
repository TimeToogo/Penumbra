<?php

namespace Storm\Api\Base;

use \Storm\Api\IEntityManager;
use \Storm\Api\IConfiguration;
use \Storm\Core\Object;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Drivers\Base;
use \Storm\Pinq;
use \Storm\Pinq\Functional;

/**
 * The Repository provides the clean api for querying on a specific
 * type of entity.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class EntityManager implements IEntityManager {
    /**
     * The DomainDatabaseMap to query.
     * 
     * @var DomainDatabaseMap
     */
    protected $DomainDatabaseMap;
    
    /**
     * @var IFunctionToASTConverter
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
    private $PersistedQueue = [];
    
    /**
     * Procedures that are awaiting execution
     * 
     * @var array 
     */
    private $ExecutionQueue = [];
    
    /**
     * Entities that are awaiting to be discarded
     * 
     * @var array 
     */
    private $DiscardedQueue = [];
    
    /**
     * Criteria of entities to discard
     * 
     * @var array 
     */
    private $DiscardedCriteriaQueue = [];    
    
    /**
     * The cache to use as the identity map for the repository
     * 
     * @var IdentityMap
     */
    private $IdentityMap;    
    
    public function __construct(
            DomainDatabaseMap $DomainDatabaseMap,
            Pinq\IFunctionToExpressionTreeConverter $FunctionToASTConverter,
            $EntityType) {
        $this->DomainDatabaseMap = $DomainDatabaseMap;
        $this->EntityMap = $this->DomainDatabaseMap->GetDomain()->GetEntityMap($EntityType);
        $this->IdentityProperties = $this->EntityMap->GetIdentityProperties();
        $this->FunctionToASTConverter = $FunctionToASTConverter;
        $this->EntityType = $EntityType;
        $this->IdentityMap = new IdentityMap($this->EntityMap, new \Storm\Utilities\Cache\MemoryCache());
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
    final public function GetEntityMap() {
        return $this->EntityMap;
    }
    
    /**
     * {@inheritDoc}
     */
    final public function GetIdentityMap() {
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
     * {@inheritDoc}
     */
    final public function SetAutoSave($AutoSave) {
        $this->AutoSave = $AutoSave;
    }
    
    /**
     * {@inheritDoc}
     */
    final public function Request() {
        return new Pinq\Request(
                $this,
                $this->FunctionToASTConverter);
    }
    
    /**
     * @return Pinq\Procedure
     */
    final public function Procedure() {
        return new Pinq\Procedure(
                $this,
                $this->FunctionToASTConverter);
    }
    
    /**
     * {@inheritDoc}
     */
    final public function Remove() {
        return new Pinq\Removal(
                $this,
                $this->FunctionToASTConverter);
    }
    
    /**
     * {@inheritDoc}
     */
    final public function LoadEntities(Object\IEntityRequest $Request) {
        $this->VerifyRequestType($Request);
        
        $Entities = $this->DomainDatabaseMap->LoadEntities($Request);
        $this->IdentityMap->CacheEntities($Entities);
        
        return $Entities;
    }
    
    /**
     * {@inheritDoc}
     */
    public function LoadData(Object\IDataRequest $Request) {
        $this->VerifyRequestType($Request);
        
        return $this->DomainDatabaseMap->LoadData($Request);
    }
    
    /**
     * {@inheritDoc}
     */
    public function LoadExists(Object\IRequest $Request) {
        $this->VerifyRequestType($Request);
        
        return $this->DomainDatabaseMap->LoadExists($Request);
    }
    
    private function VerifyRequestType(Object\IRequest $Request) {
        if($Request->GetEntityType() !== $this->EntityType) {
            throw new Object\TypeMismatchException(
                    'The supplied request is of type %s, expecting: %s', 
                    $Request->GetEntityType(), 
                    $this->EntityType);
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function LoadById($_) {
        return $this->LoadByIdValues(func_get_args());
    }
    
    /**
     * {@inheritDoc}
     */
    public function LoadByIdValues(array $IdentityValues) {
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
     * {@inheritDoc}
     */
    protected function LoadByIdentity(Object\Identity $Identity) {
        $CachedEntity = $this->IdentityMap->GetFromCache($Identity);
        if($CachedEntity instanceof $this->EntityType) {
            return $CachedEntity;
        }
        
        $Entities = $this->DomainDatabaseMap->LoadEntities(
                new Base\Object\EntityRequest(
                        $this->EntityType, 
                        $this->EntityMap->GetProperties(), 
                        [], 
                        [], 
                        new Base\Object\Criteria\MatchesPropertyDataCriteria(
                                $this->EntityType,
                                $Identity,
                                null,
                                0,
                                1)));
        
        $Entity = count($Entities) > 0 ? $Entities[0] : null;
        if($Entity instanceof $this->EntityType) {
            $this->IdentityMap->CacheEntity($Entity, $Identity);
        }
        
        return $Entity;
    }
    
    /**
     * {@inheritDoc}
     */
    public function Persist($Entity) {
        $this->VerifyEntity(__METHOD__, $Entity);
        $this->IdentityMap->CacheEntity($Entity);
        
        $this->PersistedQueue[] = $Entity;
        $this->AutoSave();
    }
    
    /**
     * {@inheritDoc}
     */
    public function PersistAll(array $Entities) {
        $this->IdentityMap->CacheEntities($Entities);
        
        $this->PersistedQueue = array_merge($this->PersistedQueue, $Entities);
        $this->AutoSave();
    }
    
    /**
     * {@inheritDoc}
     */
    public function Execute(Object\IProcedure $Procedure) {
        if($Procedure->GetEntityType() !== $this->EntityType) {
            throw new Object\TypeMismatchException('The supplied procedure is of type %s, expecting: %s', $Procedure->GetEntityType(), $this->EntityType);
        }
        $this->ExecutionQueue[] = $Procedure;
        $this->AutoSave();
    }
    
    /**
     * {@inheritDoc}
     */
    public function Discard($Entity) {
        $this->VerifyEntity(__METHOD__, $Entity);
        $this->IdentityMap->RemoveFromCache($Entity);
        $this->DiscardedQueue[] = $Entity;
        
        $this->AutoSave();
    }
    
    /**
     * {@inheritDoc}
     */
    public function DiscardBy(Object\ICriteria $Criteria) {
        $this->DiscardedCriteriaQueue[] = $Criteria;
        
        $this->AutoSave();
    }
    
    /**
     * {@inheritDoc}
     */
    public function DiscardAll(array $Entities) {
        $this->IdentityMap->RemoveAllFromCache($Entities);
        $this->DiscardedQueue = array_merge($this->DiscardedQueue, $Entities);
        $this->AutoSave();
    }
    
    /**
     * {@inheritDoc}
     */
    private function AutoSave() {
        if($this->AutoSave) {
            $this->SaveChanges();
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function SaveChanges() {
        if(count($this->PersistedQueue) === 0 && 
                count($this->ExecutionQueue) === 0 &&
                count($this->DiscardedQueue) === 0 &&
                count($this->DiscardedCriteriaQueue) === 0) {
            return;
        }
        
        $this->DomainDatabaseMap->Commit(
                $this->PersistedQueue, 
                $this->ExecutionQueue, 
                $this->DiscardedQueue, 
                $this->DiscardedCriteriaQueue);
        
        $this->ClearChanges();
    }
    
    /**
     * {@inheritDoc}
     */
    final public function GetChanges() {
        return [$this->PersistedQueue, 
                $this->ExecutionQueue, 
                $this->DiscardedQueue, 
                $this->DiscardedCriteriaQueue];
    }
    
    /**
     * {@inheritDoc}
     */
    final public function ClearChanges() {
        $this->PersistedQueue = [];
        $this->ExecutionQueue = [];
        $this->DiscardedQueue = [];
        $this->DiscardedCriteriaQueue = [];
    }
}

?>