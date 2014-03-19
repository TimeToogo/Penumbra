<?php

namespace Storm\Api\Base;

use \Storm\Api\IEntityManager;
use \Storm\Api\IRepository;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Drivers\Base\Object\Properties\Proxies\IProxyGenerator;
use \Storm\Pinq\Functional;
use \Storm\Pinq\FunctionToExpressionTreeConverter;

/**
 * The Storm class provides the api surrounding a DomainDatabaseMap.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Storm {
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
     * The supplied DomainDatabaseMap.
     * 
     * @var DomainDatabaseMap
     */
    protected $DomainDatabaseMap;
    
    /**
     * @var FunctionToASTConverter 
     */
    protected $FunctionToExpressionTreeConverter;
    
    public function __construct(
            DomainDatabaseMap $DomainDatabaseMap,
            IConnection $Connection,
            IProxyGenerator $ProxyGenerator,
            Functional\IParser $FunctionParser) {
        $this->DomainDatabaseMap = $DomainDatabaseMap;
        $this->DomainDatabaseMap->GetDatabase()->SetConnection($Connection);
        $this->DomainDatabaseMap->GetDomain()->SetProxyGenerator($ProxyGenerator);
        $this->FunctionToExpressionTreeConverter = $this->GetFunctionToExpressionTreeConverter($FunctionParser);
    }
    
    protected function GetFunctionToExpressionTreeConverter(Functional\IParser $FunctionParser) {
        return new FunctionToExpressionTreeConverter($FunctionParser);
    }
    
    /**
     * @return DomainDatabaseMap 
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
     * Saves all the changes from the repositories
     * instantiated by this storm.
     * 
     * @return void
     */
    final public function SaveChanges() {
        $PersistedQueues = [];
        $ExecutionQueues = [];
        $DiscardedQueues = [];
        $DiscardedCriteriaQueues = [];
        
        foreach($this->EntityManagers as $Repository) {
            list($PersistedQueue,
                    $ExecutionQueue,
                    $DiscardedQueue,
                    $DiscardedCriteriaQueue) = $Repository->GetChanges();
            
            $PersistedQueues[] = $PersistedQueue;
            $ExecutionQueues[] = $ExecutionQueue;
            $DiscardedQueues[] = $DiscardedQueue;
            $DiscardedCriteriaQueues[] = $DiscardedCriteriaQueue;
            
            $Repository->ClearChanges();
        }
        
        $PersistedQueue = call_user_func_array('array_merge', $PersistedQueues);
        $ExecutionQueue = call_user_func_array('array_merge', $ExecutionQueues);
        $DiscardedQueue = call_user_func_array('array_merge', $DiscardedQueues);
        $DiscardedCriteriaQueue = call_user_func_array('array_merge', $DiscardedCriteriaQueues);
        
        $this->DomainDatabaseMap->Commit(
                $PersistedQueue, 
                $ExecutionQueue, 
                $DiscardedQueue, 
                $DiscardedCriteriaQueue);
    }
    
    /**
     * Clears all the changes from the repositories
     * instantiated by this storm.
     * 
     * @return void
     */
    final public function ClearChanges() {
        foreach($this->EntityManagers as $EntityManager) {
            $EntityManager->ClearChanges();
        }
    }
}

?>
