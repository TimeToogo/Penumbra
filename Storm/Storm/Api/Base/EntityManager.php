<?php

namespace Storm\Api\Base;

use \Storm\Api\IEntityManager;
use \Storm\Core\Object;
use \Storm\Core\Mapping;
use \Storm\Drivers\Base;
use \Storm\Pinq;

/**
 * The Repository provides the clean api for querying on a specific
 * type of entity.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class EntityManager implements IEntityManager {
    /**
     * The domain database map to query.
     * 
     * @var Mapping\DomainDatabaseMap
     */
    protected $DomainDatabaseMap;
    
    /**
     * @var Pinq\Functional\IFunctionToExpressionTreeConverter
     */
    protected $FunctionToExpressionTreeConverter;
    
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
     * The storage for all the pending changes
     * 
     * @var Mapping\PendingChanges 
     */
    private $PendingChanges;
    
    /**
     * The cache to use as the identity map for the repository
     * 
     * @var IdentityMap
     */
    private $IdentityMap;    
    
    public function __construct(
            Mapping\DomainDatabaseMap $DomainDatabaseMap,
            Pinq\Functional\IFunctionToExpressionTreeConverter $FunctionToExpressionConverter,
            $EntityType) {
        $this->DomainDatabaseMap = $DomainDatabaseMap;
        $this->EntityMap = $this->DomainDatabaseMap->GetDomain()->VerifyEntityMap($EntityType);
        $this->IdentityProperties = $this->EntityMap->GetIdentityProperties();
        $this->FunctionToExpressionTreeConverter = $FunctionToExpressionConverter;
        $this->EntityType = $EntityType;
        $this->PendingChanges = new Mapping\PendingChanges();
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
                    \Storm\Utilities\Type::GetTypeOrClass($Entity));
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
                $this->FunctionToExpressionTreeConverter);
    }
    
    /**
     * @return Pinq\Procedure
     */
    final public function Procedure() {
        return new Pinq\Procedure(
                $this,
                $this->FunctionToExpressionTreeConverter);
    }
    
    /**
     * {@inheritDoc}
     */
    final public function Remove() {
        return new Pinq\Removal(
                $this,
                $this->FunctionToExpressionTreeConverter);
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
                                null, //Order By
                                0,//Range Offset
                                1 //Range Limit
                                )));
        
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
        
        $this->PendingChanges->AddEntityToPersist($Entity);
        $this->AutoSave();
    }
    
    /**
     * {@inheritDoc}
     */
    public function PersistAll(array $Entities) {
        $this->IdentityMap->CacheEntities($Entities);
        
        $this->PendingChanges->AddEntitiesToPersist($Entities);
        $this->AutoSave();
    }
    
    /**
     * {@inheritDoc}
     */
    public function Execute(Object\IProcedure $Procedure) {
        if($Procedure->GetEntityType() !== $this->EntityType) {
            throw new Object\TypeMismatchException('The supplied procedure is of type %s, expecting: %s', $Procedure->GetEntityType(), $this->EntityType);
        }
        $this->PendingChanges->AddProcedureToExecute($Procedure);
        
        $this->AutoSave();
    }
    
    /**
     * {@inheritDoc}
     */
    public function Discard($Entity) {
        $this->VerifyEntity(__METHOD__, $Entity);
        $this->IdentityMap->RemoveFromCache($Entity);
        $this->PendingChanges->AddEntityToDiscard($Entity);
        
        $this->AutoSave();
    }
    
    /**
     * {@inheritDoc}
     */
    public function DiscardBy(Object\ICriteria $Criteria) {
        $this->PendingChanges->AddCriteriaToDiscardBy($Criteria);
        
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
        if($this->PendingChanges->IsEmpty()) {
            return;
        }
        
        $this->DomainDatabaseMap->Commit($this->PendingChanges);
        
        $this->PendingChanges->Reset();
    }
    
    /**
     * {@inheritDoc}
     */
    final public function GetChanges() {
        return $this->PendingChanges;
    }
    
    /**
     * {@inheritDoc}
     */
    final public function ClearChanges() {
        $this->PendingChanges->Reset();
    }
}

?>