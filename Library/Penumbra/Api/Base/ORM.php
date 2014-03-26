<?php

namespace Penumbra\Api\Base;

use \Penumbra\Api\IEntityManager;
use \Penumbra\Api\IRepository;
use \Penumbra\Core\Mapping;
use \Penumbra\Drivers\Base\Relational\Queries\IConnection;
use \Penumbra\Drivers\Base\Object\Properties\Proxies\IProxyGenerator;
use \Penumbra\Pinq\Functional;

/**
 * The Penumbra class provides the api surrounding a DomainDatabaseMap.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ORM {
    /**
     * The entity managers.
     * 
     * @var IEntityManager[]
     */
    protected $EntityManagers = [];
    
    /**
     * The entity repositories.
     * 
     * @var IEntityManager[]
     */
    protected $Repositories = [];
    
    /**
     * The supplied domain database map.
     * 
     * @var Mapping\DomainDatabaseMap
     */
    protected $DomainDatabaseMap;
    
    /**
     * @var FunctionToASTConverter 
     */
    protected $FunctionToExpressionTreeConverter;
    
    /**
     * @var Mapping\PendingChanges 
     */
    protected $PendingChanges;
    
    public function __construct(
            Mapping\DomainDatabaseMap $DomainDatabaseMap,
            IConnection $Connection,
            IProxyGenerator $ProxyGenerator,
            Functional\IParser $FunctionParser) {
        $this->DomainDatabaseMap = $DomainDatabaseMap;
        $this->DomainDatabaseMap->GetDatabase()->SetConnection($Connection);
        $this->DomainDatabaseMap->GetDomain()->SetProxyGenerator($ProxyGenerator);
        $this->FunctionToExpressionTreeConverter = $this->GetFunctionToExpressionTreeConverter($FunctionParser);
        $this->PendingChanges = new Mapping\PendingChanges();
    }
    
    protected function GetFunctionToExpressionTreeConverter(Functional\IParser $FunctionParser) {
        return new Functional\FunctionToExpressionTreeConverter($FunctionParser);
    }
    
    /**
     * @return Mapping\DomainDatabaseMap 
     */
    final public function GetDomainDatabaseMap() {
        return $this->DomainDatabaseMap;
    }
    
    /**
     * Get the entity manager instance for a type of entity.
     * 
     * @param string|object $EntityType The entity of which the entity manager represents
     * @return IEntityManager
     */
    final public function GetEntityManger($EntityType) {
        if(is_object($EntityType)) {
            $EntityType = get_class($EntityType);
        }
        
        if(!isset($this->EntityManagers[$EntityType])) {
            $this->EntityManagers[$EntityType] = $this->ConstructEntityManager($EntityType);
        }
        
        return $this->EntityManagers[$EntityType];
    }
    
    /**
     * Get the repository instance for a type of entity.
     * 
     * @param string|object $EntityType The entity of which the repository represents
     * @return IRepository
     */
    final public function GetRepository($EntityType) {
        if(is_object($EntityType)) {
            $EntityType = get_class($EntityType);
        }
        
        if(!isset($this->Repositories[$EntityType])) {
            $this->Repositories[$EntityType] = $this->ConstructRepository($EntityType);
        }
        
        return $this->Repositories[$EntityType];
    }
    
    /**
     * Instantiates a new entity manager for the specified entity type.
     * 
     * @param string $EntityType The entity of which the entity manager represents
     * @return IEntityManager The instantiated entity manager
     */
    protected function ConstructEntityManager($EntityType) {
        return new EntityManager($this->DomainDatabaseMap, $this->FunctionToExpressionTreeConverter, $EntityType);
    }
    
    /**
     * Instantiates a new repository for the specified entity type.
     * 
     * @param string $EntityType The entity of which the repository represents
     * @return IRepository The instantiated repository
     */
    protected function ConstructRepository($EntityType) {
        return new Repository($this->GetEntityManger($EntityType), $this->FunctionToExpressionTreeConverter);
    }
    
    /**
     * Saves all the changes from the entity managers
     * instantiated by this penumbra.
     * 
     * @return void
     */
    final public function SaveChanges() {
        foreach($this->EntityManagers as $EntityManager) {
            $this->PendingChanges->Merge($EntityManager->GetChanges());
        }
        
        $this->DomainDatabaseMap->Commit($this->PendingChanges);
        $this->ClearChanges();
    }
    
    /**
     * Clears all the changes from the entity managers
     * instantiated by this penumbra.
     * 
     * @return void
     */
    final public function ClearChanges() {
        foreach($this->EntityManagers as $EntityManager) {
            $EntityManager->ClearChanges();
        }
        $this->PendingChanges->Reset();
    }
}

?>