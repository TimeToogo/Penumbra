<?php

namespace Storm\Api\Base;

use \Storm\Api\IConfiguration;
use \Storm\Core\Object;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Drivers\Base;
use \Storm\Pinq;
use \Storm\Pinq\Functional;

/**
 * The repository acts as a simple collection for 
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IEntityManager {
    
    /**
     * Gets the identity map used for this repository.
     * 
     * @return IdentityMap
     */
    public function GetIdentityMap();
    
    /**
     * Set whether or not to automatically commit every change.
     * 
     * @param boolean $AutoSave
     * @return void
     */
    public function SetAutoSave($AutoSave);
    
    /**
     * @return Pinq\Request 
     */
    public function Request();
    
    /**
     * @return Pinq\Procedure
     */
    public function Procedure();
    
    /**
     * @return Pinq\Removal 
     */
    public function Remove();
    
    /**
     * Loads an array of entities specified by the supplied request.
     * 
     * @param Object\IRequest $Request The request to load
     * @return object[]
     */
    public function LoadEntities(Object\IEntityRequest $Request);
    
    /**
     * Loads the data specified by the request
     * 
     * @param Object\IRequest $Request The request to load
     * @return array[] the loaded data
     */
    public function LoadData(Object\IDataRequest $Request);
    
    /**
     * Loads whether any entities match the request
     * 
     * @param Object\IRequest $Request The request to load
     * @return boolean
     */
    public function LoadExists(Object\IRequest $Request);
    
    /**
     * Loads an entity from given identity values or null if entity does not exist.
     * 
     * @param mixed ... The identity value(s)  
     * @return object|null The returned entity or null
     * @throws \Storm\Core\StormException
     */
    public function LoadById($_);
    
    /**
     * Adds an entity to the persistence queue. 
     * If AutoSave is enabled, the action will be commited.
     * 
     * @param object $Entity The entity to persist
     * @return void
     */
    public function Persist($Entity);
    
    /**
     * Adds an array of entities to the persistence queue. 
     * If AutoSave is enabled, the action will be commited.
     * 
     * @param array $Entities The entities to persist
     * @return void
     */
    public function PersistAll(array $Entities);
    
    /**
     * Adds a procedure to the execution queue. 
     * If AutoSave is enabled, the action will be commited.
     * 
     * @param Object\IProcedure $ProcedureBuilder The procedure to execute
     * @return void
     */
    public function Execute(Object\IProcedure $Procedure);
    
    /**
     * Adds an entity or criteria to the discardence queue. 
     * If AutoSave is enabled, the action will be commited.
     * 
     * @param object|Object\ICriteria $EntityOrCriteria The entity or criteria to discard
     * @return void
     */
    public function Discard($EntityOrCriteria);
    
    /**
     * Adds an array of entities to the discardence queue. 
     * If AutoSave is enabled, the action will be commited.
     * 
     * @param array $Entities The entities to discard
     * @return void
     */
    public function DiscardAll(array $Entities);
    
    /**
     * Commits all specified changes to the underlying DomainDatabaseMap.
     * 
     * @return void
     */
    public function SaveChanges();
    
    /**
     * Gets the pending changes.
     * 
     * @return array An array containing all the operations queues
     */
    public function GetChanges();
    
    /**
     * Clears all the pending changes awaiting to be 
     * commited to underlying DomainDatabaseMap.
     * 
     * @return void
     */
    public function ClearChanges();
}

?>