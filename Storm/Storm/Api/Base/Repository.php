<?php

namespace Storm\Api\Base;

use \Storm\Core\Object;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Drivers\Base;
use \Storm\Drivers\Fluent\Object\Closure;

class Repository {
    /**
     * @var DomainDatabaseMap 
     */
    protected $DomainDatabaseMap;
    
    /**
     * @var string 
     */
    protected $EntityType;
    
    /**
     * @var Object\EntityMap 
     */
    protected $EntityMap;
    
    /**
     * @var IProperty[] 
     */
    protected $IdentityProperties;
    private $AutoSave;
    private $PersistedQueue = array();
    private $ExecutionQueue = array();
    private $DiscardedQueue = array();
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
     * @return Fluent\RequestBuilder
     */
    final public function Request() {
        return new Fluent\RequestBuilder($this->EntityMap, $this->GetClosureToASTConverter());
    }
    
    /**
     * @return Fluent\ProcedureBuilder
     */
    final function Procedure(\Closure $ProcedureClosure) {
        return new Fluent\ProcedureBuilder($this->EntityMap, $this->GetClosureToASTConverter(), $ProcedureClosure);
    }
    
    /**
     * @return Fluent\CriterionBuilder
     */
    final function Criterion() {
        return new Fluent\CriterionBuilder($this->EntityMap, $this->GetClosureToASTConverter());
    }
    
    public function Load(Fluent\RequestBuilder $RequestBuilder) {
        return $this->LoadRequest($RequestBuilder->BuildRequest());
    }
        
    public function LoadRequest(Object\IRequest $Request) {
        if($Request->GetEntityType() !== $this->EntityType) {
            throw new \Exception();//TODO: error messages;
        }
        return $this->DomainDatabaseMap->Load($Request);
    }
    
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
    
    protected function LoadByIdentity(Object\Identity $Identity) {
        return $this->DomainDatabaseMap->Load(
                new Base\Object\Request(
                        $this->EntityType, 
                        $this->EntityMap->GetProperties(), 
                        true, 
                        new Base\Object\Criteria\MatchesCriterion($Identity)));
    }
    
    public function Persist($Entity) {
        $this->VerifyEntity($Entity);
        $this->PersistedQueue[] = $Entity;
        $this->AutoSave();
    }
    public function PersistAll(array $Entities) {
        $this->PersistedQueue = array_merge($this->PersistedQueue, $Entities);
        $this->AutoSave();
    }
    
    public function Execute(Fluent\ProcedureBuilder $ProcedureBuilder) {
        $this->ExecuteProcedure($ProcedureBuilder->BuildProcedure());
    }
    
    public function ExecuteProcedure(Object\IProcedure $Procedure) {
        if($Procedure->GetEntityType() !== $this->EntityType) {
            throw new \Exception();//TODO: error messages;
        }
        $this->ExecutionQueue[] = $Procedure;
        $this->AutoSave();
    }
    
    public function Discard(&$Entity) {
        $this->VerifyEntity($Entity);
        $this->DiscardedQueue[] = $Entity;
        $this->AutoSave();
    }
    public function DiscardAll(array $Entities) {
        $this->DiscardedQueue = array_merge($this->DiscardedQueue, $Entities);
        $this->AutoSave();
    }
    
    public function DiscardWhere(Object\ICriterion $Criterion) {
        $this->DiscardedCriterionQueue[] = $Criterion;
        $this->AutoSave();
    }
    
    private function AutoSave() {
        if($this->AutoSave) {
            $this->SaveChanges();
        }
    }
    
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