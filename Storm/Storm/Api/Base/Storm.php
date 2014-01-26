<?php

namespace Storm\Api\Base;

use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Drivers\Fluent\Object\Closure;

/**
 * The Storm class provides the api surrounding a DomainDatabaseMap.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Storm {
    /**
     * The entity repositories.
     * 
     * @var Repository[]
     */
    protected $Repositories;
    
    /**
     * The supplied DomainDatabaseMap.
     * 
     * @var DomainDatabaseMap
     */
    protected $DomainDatabaseMap;
    
    /**
     * @var ClosureToASTConverter 
     */
    protected $ClosureToASTConverter;
    
    public function __construct(
            DomainDatabaseMap $DomainDatabaseMap,
            IConnection $Connection,
            Closure\IReader $ClosureReader, 
            Closure\IParser $ClosureParser) {
        $this->DomainDatabaseMap = $DomainDatabaseMap;
        $this->DomainDatabaseMap->GetDatabase()->SetConnection($Connection);
        $this->ClosureToASTConverter = $this->GetClosureToASTConverter($ClosureReader, $ClosureParser);
    }
    
    protected function GetClosureToASTConverter(
            Closure\IReader $ClosureReader, 
            Closure\IParser $ClosureParser) {
        return new ClosureToASTConverter($ClosureReader, $ClosureParser);
    }
    
    /**
     * @return DomainDatabaseMap 
     */
    final public function GetDomainDatabaseMap() {
        return $this->DomainDatabaseMap;
    }
    
    /**
     * Get the repository instance for a type of entity.
     * 
     * @param string|object $EntityType The entity of which the repository represents
     * @return Repository
     */
    public function GetRepository($EntityType) {
        if(is_object($EntityType)) {
            $EntityType = get_class($EntityType);
        }
        
        if(!isset($this->Repositories[$EntityType])) {
            $this->Repositories[$EntityType] = $this->ConstructRepository($EntityType);
        }
        
        return $this->Repositories[$EntityType];
    }
    
    /**
     * Instantiates a new repository for the specified entity type.
     * 
     * @param string $EntityType The entity of which the repository represents
     * @return Repository The instantiated repository
     */
    protected function ConstructRepository($EntityType) {
        return new Repository($this->DomainDatabaseMap, $this->ClosureToASTConverter, $EntityType);
    }
    
    /**
     * Saves all the changes from the repositories
     * instantiated by this storm.
     * 
     * @return void
     */
    final public function SaveChanges() {
        $PersistedQueues = array();
        $ExecutionQueues = array();
        $DiscardedQueues = array();
        $DiscardedCriterionQueues = array();
        
        foreach($this->Repositories as $Repository) {
            list($PersistedQueue,
            $ExecutionQueue,
            $DiscardedQueue,
            $DiscardedCriterionQueue) = $Repository->GetChanges();
            $PersistedQueues[] = $PersistedQueue;
            $ExecutionQueues[] = $ExecutionQueue;
            $DiscardedQueues[] = $DiscardedQueue;
            $DiscardedCriterionQueues[] = $DiscardedCriterionQueue;
            
            $Repository->ClearChanges();
        }
        
        $PersistedQueue = call_user_func_array('array_merge', $PersistedQueues);
        $ExecutionQueue = call_user_func_array('array_merge', $ExecutionQueues);
        $DiscardedQueue = call_user_func_array('array_merge', $DiscardedQueues);
        $DiscardedCriterionQueue = call_user_func_array('array_merge', $DiscardedCriterionQueues);
        
        $this->DomainDatabaseMap->Commit(
                $PersistedQueue, 
                $ExecutionQueue, 
                $DiscardedQueue, 
                $DiscardedCriterionQueue);
    }
    
    /**
     * Clears all the changes from the repositories
     * instantiated by this storm.
     * 
     * @return void
     */
    final public function ClearChanges() {
        foreach($this->Repositories as $Repository) {
            $Repository->ClearChanges();
        }
    }
}

?>
