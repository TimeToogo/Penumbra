<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Object;
use \Storm\Core\Relational;
use \Storm\Core\Containers\Registrar;
use \Storm\Core\Containers\Map;

/**
 * The domain database map class provides the nessecary data and api to map between
 * entity object graphs and a relational database.
 * 
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class DomainDatabaseMap {
    
    /**
     * The Domain instance representing the domain of this map.
     * 
     * @var Object\Domain
     */
    private $Domain;
    
    /**
     * The Database instance representing the database of this map.
     * 
     * @var Relational\Database
     */
    private $Database;
    
    /**
     * A collection of entity relational mapping instances.
     * 
     * @var IEntityRelationalMap[] 
     */
    private $EntityRelationalMaps = [];
    
    /**
     * The entity relational maps, indexed by their primary key table name.
     * 
     * @var IEntityRelationalMap[] 
     */
    private $EntityRelationalMapsByPrimaryKeyTable = [];
    
    public function __construct() {
        $this->Domain = $this->Domain();
        $this->Database = $this->Database();
        
        $this->OnInitialize($this->Domain, $this->Database);
        $this->Domain->InitializeEntityMaps();
        $this->Database->InitializeTables();
        
        $Registrar = new Registrar(IEntityRelationalMap::IEntityRelationalMapType);
        $this->RegisterEntityRelationalMaps($Registrar);
        foreach ($Registrar->GetRegistered() as $EntityRelationalMap) {
            $this->AddEntityRelationMap($EntityRelationalMap);
        }
        foreach ($this->EntityRelationalMaps as $EntityRelationalMap) {
            $EntityRelationalMap->InitializeRelationshipMappings($this);
        }
    }
    
    protected function OnInitialize(Object\Domain $Domain, Relational\Database $Database) {}
    
    /**
     * This class can be very expensive to instantiate, so this 
     * provides a factory closure if required.
     * 
     * @param ... mixed The constructor arguments
     */
    final public static function Factory() {
        $Arguments = func_get_args();
        $CalledClass = get_called_class();
        return function () use (&$Arguments, $CalledClass) {
            $Reflection = new \ReflectionClass($CalledClass);
            
            return $Reflection->newInstanceArgs($Arguments);
        };
    }
    
    /**
     * The method to specify the domain instance.
     * 
     * @return Object\Domain
     */
    protected abstract function Domain();
    
    /**
     * The method to specify the database instance.
     * 
     * @return Relational\Database
     */
    protected abstract function Database();
    
    /**
     * This is where you register your EntityRelationalMap classes.
     */
    protected abstract function RegisterEntityRelationalMaps(Registrar $Registrar);
    
    /**
     * @return Object\Domain The Domain instance
     */
    final public function GetDomain() {
        return $this->Domain;
    }
    
    /**
     * @return Relational\Database The Database instance
     */
    final public function GetDatabase() {
        return $this->Database;
    }
    
    /**
     * Adds an entity relational map instance to this domain database map.
     * 
     * @param IEntityRelationalMap $EntityRelationalMap The entity relational mapping class.
     * @throws MappingException If the entity is not part of the given domain
     */
    private function AddEntityRelationMap(IEntityRelationalMap $EntityRelationalMap) {
        $EntityRelationalMap->Initialize($this);
        
        $EntityType = $EntityRelationalMap->GetEntityType();
        if(!$this->Domain->HasEntityMap($EntityType)) {
            throw new MappingException('The supplied entity relational map for %s is not part of the given domain.', $EntityType);
        }
        
        $this->EntityRelationalMaps[$EntityType] = $EntityRelationalMap;
        $this->EntityRelationalMapsByPrimaryKeyTable[$EntityRelationalMap->GetPrimaryKeyTable()->GetName()] = $EntityRelationalMap;
    }
    
    /**
     * Returns if this contains an relational map for the given type of entity.
     * 
     * @param string $EntityType The type of the entity
     * @return boolean
     */
    final public function HasEntityRelationalMap($EntityType) {
        return $this->GetEntityRelationalMap($EntityType) !== null;
    }
    
    /**
     * Gets the registered relational map for the given entity type.
     * 
     * @param string $EntityType The type of the entity (sub classes will resolve)
     * @return IEntityRelationalMap|null The relational map or null if not found
     */
    final public function GetEntityRelationalMap($EntityType) {
        if(isset($this->EntityRelationalMaps[$EntityType])) {
            return $this->EntityRelationalMaps[$EntityType];
        }
        else {
            $ParentType = get_parent_class($EntityType);
            if($ParentType === false) {
                return null;
            }
            else {
                return $this->GetEntityRelationalMap($ParentType);
            }
        }
    }
    
    /**
     * Gets the registered relational map for the given primary key table name.
     * 
     * @param string $TableName The name of primary key table.
     * @return IEntityRelationalMap|null The relational map or null if not found
     */
    final public function GetEntityRelationalMapByPrimaryKeyTable($TableName) {
        return isset($this->EntityRelationalMapsByPrimaryKeyTable[$TableName]) ?
                $this->EntityRelationalMapsByPrimaryKeyTable[$TableName] : null;
    }
    
    /**
     * Verifies that an entity type is mapped and returns the relational map if is found.
     * 
     * @param string $EntityType The entity type
     * @return IEntityRelationalMap The registered entity relational map
     * @throws UnmappedEntityException If the relational map has not been registered
     */
    final protected function VerifyEntityTypeIsMapped($EntityType) {
        $EntityRelationalMap = $this->GetEntityRelationalMap($EntityType);
        if($EntityRelationalMap === null) {
            throw new UnmappedEntityException('The entity %s is not mapped within this domain database map', $EntityType);
        }
        
        return $EntityRelationalMap;
    }
    
    /**
     * Loads all entities that are specified from the given request.
     * 
     * @param Object\IRequest $Request The request to load
     * @return object[] All matching entities are retrieved as an array
     */
    final public function LoadEntities(Object\IEntityRequest $Request) {
        $EntityType = $Request->GetEntityType();
        $EntityRelationalMap = $this->VerifyEntityTypeIsMapped($EntityType);
        
        $Select = $this->MapEntityRequest($Request);
        $ResultRows = $this->Database->Load($Select);
        
        $RevivalDataArray = $EntityRelationalMap->MapResultRowsToRevivalData($ResultRows);
        
        return $this->Domain->ReviveEntities($EntityType, $RevivalDataArray);        
    }
    
    /**
     * Loads the data specified by the supplied request
     * 
     * @param Object\IRequest $Request The request to load
     * @return array[] The loaded data
     */
    final public function LoadData(Object\IDataRequest $Request) {
        $AliasReviveFunctionMap = [];
        $Select = $this->MapDataRequest($Request, $AliasReviveFunctionMap);
        $Data = $this->Database->Load($Select);
        foreach ($Data as &$Row) {
            foreach ($AliasReviveFunctionMap as $Alias => $ReviveFunction) {
                $Row[$Alias] = $ReviveFunction($Row[$Alias]);
            }
        }
        
        return $Data;
    }
    
    /**
     * Returns whether any entities match the given request
     * 
     * @param Object\IRequest $Request The request to load
     * @return bool
     */
    final public function LoadExists(Object\IRequest $Request) {
        $Select = $this->MapToExistsSelect($Request);
        return $this->Database->Load($Select);
    }
    
    /**
     * Commits the supplied operations to the underlying database within a transactional scope.
     * 
     * @param array $EntitiesToPersist The entities to persist
     * @param array $ProceduresToExecute The procedures to execute
     * @param array $EntitiesToDiscard The entities to discard
     * @param array $CriteriaToDiscard The criteria of entities to discard
     * @return void
     */
    final public function Commit(PendingChanges $PendingChanges) {
        
        $UnitOfWork = $this->Domain->BuildUnitOfWork(
                $PendingChanges->GetEntitiesToPersist(), 
                $PendingChanges->GetProceduresToExecute(), 
                $PendingChanges->GetEntitiesToDiscard(), 
                $PendingChanges->GetCriteriaToDiscardBy());
        
        $Transaction = new Relational\Transaction();
        $this->MapUnitOfWorkToTransaction($UnitOfWork, $Transaction);
        
        $this->Database->Commit($Transaction);
    }
    
    /**
     * Maps a given object request to the relational equivalent.
     * 
     * @param Object\IRequest $Request The request
     * @return Relational\ExistsSelect The exists relational select
     */
    protected abstract function MapToExistsSelect(Object\IRequest $Request);
    
    /**
     * Maps a given entity request to the relational equivalent.
     * 
     * @param Object\IEntityRequest $EntityRequest The entity request
     * @return Relational\ResultSetSelect The equivalent relational select
     */
    protected abstract function MapEntityRequest(Object\IEntityRequest $EntityRequest);
    
    /**
     * Maps a given data request to the relational equivalent.
     * 
     * @param Object\IDataRequest $DataRequest The data request
     * @param array|null $AliasReviverMap Returns any function to revive the loaded data
     * @return Relational\DataSelect The data select
     */
    protected abstract function MapDataRequest(Object\IDataRequest $DataRequest, array &$AliasReviveFuncionMap);
    
    
    /**
     * @return Relational\Update
     */
    protected abstract function MapProcedure(Object\IProcedure $Procedure);
    
    /**
     * Maps the supplied object criteria to the equivalent delete
     * 
     * @param Object\ICriteria $Criteria The object criteria to map
     * @return Relational\Delete The delete
     */
    protected abstract function MapCriteriaToDelete(Object\ICriteria $Criteria);
    
    
    /**
     * Maps a unit of work to the transaction containing the ne.
     * 
     * @param Object\UnitOfWork $UnitOfWork The unit of work to map
     * @param Relational\Transaction $Transaction The transaction to map to
     * @return void
     */
    private function MapUnitOfWorkToTransaction(
            Object\UnitOfWork $UnitOfWork, 
            Relational\Transaction $Transaction) {
        $Mapping = new UnitOfWorkTransactionMapping($Transaction);
        
        foreach($UnitOfWork->GetPersistenceDataGroups() as $EntityType => $PersistenceDataGroup) {
            $EntityRelationalMap = $this->VerifyEntityTypeIsMapped($EntityType);
            $Mapping->MapPersistenceDataArray($EntityRelationalMap, $PersistenceDataGroup);
        }
        
        foreach($UnitOfWork->GetExecutedProcedures() as $Procedure) {
            $Transaction->AddUpdate($this->MapProcedure($Procedure));
        }
        
        foreach($UnitOfWork->GetDiscardenceDataGroups() as $EntityType => $DiscardenceDataGroup) {
            $EntityRelationalMap = $this->VerifyEntityTypeIsMapped($EntityType);
            $Mapping->MapDiscardenceDataArray($EntityRelationalMap, $DiscardenceDataGroup);
        }
        
        foreach($UnitOfWork->GetDiscardedCriteria() as $DiscardedCriteria) {
            $Transaction->AddDelete($this->MapCriteriaToDelete($DiscardedCriteria));
        }
    }    
}

?>