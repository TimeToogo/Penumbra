<?php

namespace Storm\Api\Base;

use \Storm\Core\Object;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Drivers\Base;
use \Storm\Drivers\Fluent\Object\Closure;

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
     * The type of entities for this repository.
     * 
     * @var string
     */
    protected $EntityType;
    
    /**
     * The EntityMap for this repository.
     * 
     * @var Object\EntityMap
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
    private $AutoSave;
    
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
    
    public function __construct(
            DomainDatabaseMap $DomainDatabaseMap, 
            $EntityType, 
            $AutoSave) {
        $this->DomainDatabaseMap = $DomainDatabaseMap;
        $this->EntityType = $EntityType;
        $this->EntityMap = $this->DomainDatabaseMap->GetDomain()->GetEntityMap($EntityType);
        $this->IdentityProperties = $this->EntityMap->GetIdentityProperties();
        $this->AutoSave = $AutoSave;
    }
    
    /**
     * Verifies an entity to be valid for use in this repository.
     * 
     * @param object $Entity The entity to verify
     * @throws \InvalidArgumentException
     */
    final protected function VerifyEntity($Entity) {
        if(!($Entity instanceof $this->EntityType)) {
            throw new \InvalidArgumentException('$Entity must be a valid instance of ' . $this->EntityType);
        }
    }
    
    //TODO: Dependency Injection
    protected function GetClosureToASTConverter() {
        static $ClosureToASTConverter = null;
        if($ClosureToASTConverter === null) {
            $ClosureToASTConverter = new Closure\ClosureToASTConverter(
                new Closure\Implementation\File\Reader(), 
                new Closure\Implementation\PHPParser\Parser());
        }
        
        return $ClosureToASTConverter;
    }
    
    /**
     * Quick access to a new RequestBuilder instance.
     * 
     * @return Fluent\RequestBuilder 
     */
    final public function Request() {
        return new Fluent\RequestBuilder($this->EntityMap, $this->GetClosureToASTConverter());
    }
    
    /**
     * Quick access to a new ProcedureBuilder instance.
     * 
     * @return Fluent\ProcedureBuilder
     */
    final function Procedure(\Closure $ProcedureClosure) {
        return new Fluent\ProcedureBuilder($this->EntityMap, $this->GetClosureToASTConverter(), $ProcedureClosure);
    }
    
    /**
     * Quick access to a new CriterionBuilder instance.
     * 
     * @return Fluent\CriterionBuilder
     */
    final function Criterion() {
        return new Fluent\CriterionBuilder($this->EntityMap, $this->GetClosureToASTConverter());
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
     * @throws \Exception
     */
    public function LoadRequest(Object\IRequest $Request) {
        if($Request->GetEntityType() !== $this->EntityType) {
            throw new \Exception();//TODO: error messages;
        }
        return $this->DomainDatabaseMap->Load($Request);
    }
    
    /**
     * Loads an entity from given identity values or null if entity does not exist.
     * 
     * @param mixed ... The identity value(s)  
     * @return object|null The returned entity or null
     * @throws \Exception
     */
    public function LoadById($_) {
        $IdentityValues = func_get_args();
        if(count($IdentityValues) !== count($this->IdentityProperties)) {
            throw new \Exception();
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
        return $this->DomainDatabaseMap->Load(
                new Base\Object\Request(
                        $this->EntityType, 
                        $this->EntityMap->GetProperties(), 
                        true, 
                        new Base\Object\Criteria\MatchesCriterion($Identity)));
    }
    
    /**
     * Adds an entity to the persistence queue. 
     * If AutoSave is enabled, the action will be commited.
     * 
     * @param object $Entity The entity to persist
     * @return void
     */
    public function Persist($Entity) {
        $this->VerifyEntity($Entity);
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
            throw new \Exception();//TODO: error messages;
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
            $this->VerifyEntity($EntityOrCriterion);
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
        
        $this->PersistedQueue = array();
        $this->ExecutionQueue = array();
        $this->DiscardedQueue = array();
        $this->DiscardedCriterionQueue = array();
    }
}

?>