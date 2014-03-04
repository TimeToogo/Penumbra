<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Object;
use \Storm\Core\Relational;
use \Storm\Core\Containers\Registrar;
use \Storm\Core\Containers\Map;

/**
 * The DomainDatabaseMap class provides the nessecary data and api to map between a
 * entity instances and a relational database.
 * 
 * Currently this has become a minor God-Object and should be broken up into more 
 * consice and well-defined classes. This could be a bit of a refactoring job as it relied heavily upon.
 * Currently I have organized the groups of behavor into editor fold tags. 
 * These should become individual classes.
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

        $Registrar = new Registrar(IEntityRelationalMap::IEntityRelationalMapType);
        $this->RegisterEntityRelationalMaps($Registrar);
        foreach ($Registrar->GetRegistered() as $EntityRelationalMap) {
            $this->AddEntityRelationMap($EntityRelationalMap);
        }
        foreach ($this->EntityRelationalMaps as $EntityRelationalMap) {
            $EntityRelationalMap->InitializeRelationshipMappings($this);
        }
    }
    
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
        return isset($this->EntityRelationalMaps[$EntityType]);
    }
    
    /**
     * Gets the registered relational map for the given entity type.
     * 
     * @param string $EntityType The type of the entity (sub classes will resolve)
     * @return IEntityRelationalMap|null The relational map or null if not found
     */
    final public function GetEntityRelationalMap($EntityType) {
        if($this->HasEntityRelationalMap($EntityType))
            return $this->EntityRelationalMaps[$EntityType];
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
     * Loads all entities that are specified from the given request instance.
     * 
     * @param Object\IRequest $ObjectRequest The request to load
     * @return array|object|null Depending on the supplied request, either all the entities are
     * returned as an array or the first is returned or null if none are found.
     */
    final public function Load(Object\IRequest $ObjectRequest) {
        $EntityType = $ObjectRequest->GetEntityType();
        $EntityRelationalMap = $this->VerifyEntityTypeIsMapped($EntityType);
        
        $RelationalRequest = $this->MapRequest($ObjectRequest);
        
        $ResultRows = $this->Database->Load($RelationalRequest);
        
        $RevivalDataArray = $EntityRelationalMap->MapResultRowsToRevivalData($this->Database, $ResultRows);
        
        $RevivedEntities = $this->Domain->ReviveEntities($EntityType, $RevivalDataArray);
        
        if($ObjectRequest->IsSingleEntity()) {
            return count($RevivedEntities) > 0 ? reset($RevivedEntities) : null;
        }
        else {
            return $RevivedEntities;
        }
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
    final public function Commit(
            array $EntitiesToPersist,
            array $ProceduresToExecute,
            array $EntitiesToDiscard,
            array $CriteriaToDiscard) {
        
        $UnitOfWork = $this->Domain->BuildUnitOfWork(
                $EntitiesToPersist, 
                $ProceduresToExecute, 
                $EntitiesToDiscard, 
                $CriteriaToDiscard);
        
        $Transaction = new Relational\Transaction();
        $this->MapUnitOfWorkToTransaction($UnitOfWork, $Transaction);
        
        $this->Database->Commit($Transaction);
    }
    
    // <editor-fold defaultstate="collapsed" desc="Request  mappers">
    
    /**
     * @access private
     * 
     * Maps a given object request to the relational equivalent.
     * 
     * @param IRequest $ObjectRequest The object request
     * @return Relational\Request The equivalent relational request
     */
    final public function MapRequest(Object\IRequest $ObjectRequest) {
        $EntityRelationalMap = $this->VerifyEntityTypeIsMapped($ObjectRequest->GetEntityType());
        
        $RelationalRequest = new Relational\Request($EntityRelationalMap->GetCriterion($this->Database));
        $EntityRelationalMap->MapPropetiesToRelationalRequest($RelationalRequest, $ObjectRequest->GetProperties());
        
        $this->MapCriterion($EntityRelationalMap, $ObjectRequest->GetCriterion(), $RelationalRequest->GetCriterion());
        
        return $RelationalRequest;
    }
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Procedure mappers">
    
    /**
     * @access private
     * 
     * Maps a supplied object procedure to an equivalent relational procedure.
     * 
     * @param Object\IProcedure $ObjectProcedure The object procedure
     * @return Relational\Procedure The equivalent relational procedure
     */
    final public function MapProcedure(Object\IProcedure $ObjectProcedure) {
        $EntityRelationalMap = $this->VerifyEntityTypeIsMapped($ObjectProcedure->GetEntityType());
        
        $RelationalProcedure = new Relational\Procedure(
                $EntityRelationalMap->GetMappedPersistTables(), $EntityRelationalMap->GetCriterion($this->Database));
 
        $this->MapCriterion($EntityRelationalMap, $ObjectProcedure->GetCriterion(), $RelationalProcedure->GetCriterion());
        foreach($this->MapExpressions($EntityRelationalMap, $ObjectProcedure->GetExpressions()) as $MappedExpression) {
            $RelationalProcedure->AddExpression($MappedExpression);
        }
        
        return $RelationalProcedure;
    }
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Criteria mappers">
    
    /**
     * @access private
     * 
     * Maps the supplied object criterion the the relational equivalent.
     * 
     * @param Object\ICriterion $ObjectCriterion The object criterion to map
     * @return Relational\Criterion The relational equivalent
     */
    private function MapObjectCriterion(Object\ICriterion $ObjectCriterion) {
        $EntityRelationalMap = $this->VerifyEntityTypeIsMapped($ObjectCriterion->GetEntityType());
        
        $RelationalCriterion = $EntityRelationalMap->GetCriterion($this->Database);
        $this->MapCriterion(
                $EntityRelationalMap, 
                $ObjectCriterion, 
                $RelationalCriterion);
        
        return $RelationalCriterion;
    }
    
    /**
     * @access private
     * 
     * Maps the supplied object criterion the the relational equivalent.
     * 
     * @param IEntityRelationalMap $EntityRelationalMap The relational map of the object criterion
     * @param Object\ICriterion $ObjectCriterion The object criterion to map
     * @param Relational\Criterion $RelationalCriterion The relational criterion to map to
     * @return void
     */
    private function MapCriterion(IEntityRelationalMap $EntityRelationalMap,
            Object\ICriterion $ObjectCriterion, Relational\Criterion $RelationalCriterion) {
        
        if ($ObjectCriterion->IsConstrained()) {
            foreach ($this->MapExpressions($EntityRelationalMap, $ObjectCriterion->GetPredicateExpressions()) as $PredicateExpression) {
                $RelationalCriterion->AddPredicateExpression($PredicateExpression);
            }
        }
        
        if ($ObjectCriterion->IsOrdered()) {
            $ExpressionAscendingMap = $ObjectCriterion->GetOrderByExpressionsAscendingMap();
            
            foreach ($ExpressionAscendingMap as $Expression) {
                $IsAscending = $ExpressionAscendingMap[$Expression];
                $Expressions = $this->MapExpression($EntityRelationalMap, $Expression);
                foreach($Expressions as $Expression) {
                    $RelationalCriterion->AddOrderByExpression($Expression, $IsAscending);
                }
            }
        }
        
        if ($ObjectCriterion->IsGrouped()) {
            foreach ($this->MapExpressions($EntityRelationalMap, $ObjectCriterion->GetGroupByExpressions()) as $GroupByExpression) {
                $RelationalCriterion->AddGroupByExpression($GroupByExpression);
            }
        }
        
        if ($ObjectCriterion->IsRanged()) {
            $RelationalCriterion->SetRangeOffset($ObjectCriterion->GetRangeOffset());
            $RelationalCriterion->SetRangeAmount($ObjectCriterion->GetRangeAmount());
        }
    }
    // </editor-fold>
        
    // <editor-fold defaultstate="collapsed" desc="Expression mapping">
    
    /**
     * @access private
     * 
     * @param IEntityRelationalMap $EntityRelationalMap
     * @param Object\Expressions\Expression $Expressions
     * @return Relational\Expression[] The equivalent expressions
     */
    private function MapExpressions(IEntityRelationalMap $EntityRelationalMap, array $Expressions) {
        return call_user_func_array('array_merge', array_map(
                function ($Expression) use (&$EntityRelationalMap) {
                    return $this->MapExpression($EntityRelationalMap, $Expression);
                }, $Expressions));
    }


    /**
     * @access private
     * 
     * Maps the given object expression to the relational equivalent.
     * This will return an array as it is not a one-to-one mapping.
     * 
     * @return Relational\Expression[] The equivalent expressions
     */
    protected abstract function MapExpression(IEntityRelationalMap $EntityRelationalMap, Object\Expressions\Expression $Expression);

    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Entity Persistence mapping">
    
    /**
     * @access private
     * 
     * Maps a unit of work instance to the transactional equivalent.
     * 
     * @param Object\UnitOfWork $UnitOfWork The unit of work to map
     * @param Relational\Transaction $Transaction The transaction to map to
     * @return void
     */
    private function MapUnitOfWorkToTransaction(
            Object\UnitOfWork $UnitOfWork, 
            Relational\Transaction $Transaction) {
        
        foreach($UnitOfWork->GetPersistenceDataGroups() as $EntityType => $PersistenceDataGroup) {
            $this->MapPersistenceDataToTransaction($UnitOfWork, $Transaction, $PersistenceDataGroup);
        }
        
        foreach($UnitOfWork->GetExecutedProcedures() as $Procedure) {
            $Transaction->Execute($this->MapProcedure($Procedure));
        }
        
        foreach($UnitOfWork->GetDiscardenceDataGroups() as $EntityType => $DiscardedIdentityGroup) {
            $EntityRelationalMap = $this->EntityRelationalMaps[$EntityType];
            $ResultRows = $this->MapEntityDataToTransaction($UnitOfWork, $Transaction, $EntityRelationalMap, $DiscardedIdentityGroup);
            foreach($ResultRows as $ResultRow) {
                $Transaction->DiscardAll($ResultRow->GetPrimaryKeys());
            }            
        }
        
        foreach($UnitOfWork->GetDiscardedCriteria() as $DiscardedCriterion) {
            $Transaction->DiscardWhere($this->MapObjectCriterion($DiscardedCriterion));
        }
    }
    
    /**
     * @access private
     * 
     * Maps the supplied persistence data to a transaction while providing a callback to 
     * to supply the unit of work the generated identities.
     * 
     * @param Object\UnitOfWork $UnitOfWork 
     * @param Relational\Transaction $Transaction
     * @param Object\PersistenceData[] $PersistenceDataArray
     * @return Relational\ResultRows[]
     */
    private function MapPersistenceDataToTransaction(
            Object\UnitOfWork $UnitOfWork, 
            Relational\Transaction $Transaction,
            array $PersistenceDataArray) {
        if(count($PersistenceDataArray) === 0) {
            return;
        }
        
        $EntityRelationalMap = $this->EntityRelationalMaps[reset($PersistenceDataArray)->GetEntityType()];
        $PrimaryKeyTable = $EntityRelationalMap->GetPrimaryKeyTable();
        $ResultRows = $this->MapEntityDataToTransaction($UnitOfWork, $Transaction, $EntityRelationalMap, $PersistenceDataArray);
        
        $RowsWithoutPrimaryKeys = [];
        $PersistenceDataToSupply = [];
        foreach($ResultRows as $Key => $ResultRow) {
            $Transaction->PersistAll($ResultRow->GetRows());
            
            $PrimaryKeyRow = $ResultRow->GetRow($PrimaryKeyTable);
            
            if(!$PrimaryKeyRow->HasPrimaryKey()) {
                $PersistenceDataToSupply[$Key] = $PersistenceDataArray[$Key];
                $RowsWithoutPrimaryKeys[$Key] = $PrimaryKeyRow;
            }
        }
        //Adds a callback to supply the unit of work the generated identity after persistence.
        $Transaction->SubscribeToPostPersistEvent(
                $PrimaryKeyTable, 
                function () use (&$UnitOfWork, &$EntityRelationalMap, &$RowsWithoutPrimaryKeys, &$PersistenceDataToSupply) {
                    $Identities = $EntityRelationalMap->MapPrimaryKeysToIdentities($RowsWithoutPrimaryKeys);
                    foreach($Identities as $Key => $Identity) {
                        $UnitOfWork->SupplyIdentity($PersistenceDataToSupply[$Key], $Identity);
                    }
                });
        
        return $ResultRows;
    }
    
    /**
     * @access private
     * 
     * Maps the supplied entity data to result rows and maps any relationship changes.
     * 
     * @param Object\UnitOfWork $UnitOfWork
     * @param Relational\Transaction $Transaction
     * @param IEntityRelationalMap $EntityRelationalMap
     * @param Object\EntityData $EntityDataArray
     * @return Relational\ResultRows[]
     */
    private function MapEntityDataToTransaction(
            Object\UnitOfWork $UnitOfWork, Relational\Transaction $Transaction, 
            IEntityRelationalMap $EntityRelationalMap, array $EntityDataArray) {
        
        $DataPropertyColumnMappings = $EntityRelationalMap->GetDataPropertyColumnMappings();
        $EntityPropertyToOneRelationMappings = $EntityRelationalMap->GetEntityPropertyToOneRelationMappings();
        $CollectionPropertyToManyRelationMappings = $EntityRelationalMap->GetCollectionPropertyToManyRelationMappings();
        
        $ResultRowArray = [];
        foreach($EntityDataArray as $Key => $EntityData) {
            $ResultRowArray[$Key] = $EntityRelationalMap->ResultRow();
        }
        
        foreach($DataPropertyColumnMappings as $DataPropertyColumnMapping) {
            $DataPropertyColumnMapping->Persist($EntityDataArray, $ResultRowArray);
        }
        
        foreach($EntityDataArray as $Key => $EntityData) {
            $ResultRow = $ResultRowArray[$Key];
                        
            foreach($EntityPropertyToOneRelationMappings as $EntityPropertyToOneRelationMapping) {
                $RelationshipChange = $EntityData[$EntityPropertyToOneRelationMapping->GetProperty()];
                $MappedRelationshipChange = 
                        $this->MapRelationshipChanges(
                                $UnitOfWork, $Transaction, $EntityRelationalMap,
                                [$RelationshipChange])[0];
                
                $EntityPropertyToOneRelationMapping->Persist($Transaction, $ResultRow, $MappedRelationshipChange);
            }
            
            foreach($CollectionPropertyToManyRelationMappings as $CollectionPropertyToManyRelationMapping) {
                $RelationshipChanges = $EntityData[$CollectionPropertyToManyRelationMapping->GetProperty()];
                $MappedRelationshipChanges = $this->MapRelationshipChanges(
                        $UnitOfWork, $Transaction, $EntityRelationalMap, $RelationshipChanges);
                
                $CollectionPropertyToManyRelationMapping->Persist($Transaction, $ResultRow, $MappedRelationshipChanges);
            }
        }
        
        return $ResultRowArray;
    }
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Relationship mappers">
        
    /**
     * @return Relational\DiscardedRelationship[]
     */
    final public function MapDiscardedRelationships(
            IEntityRelationalMap $EntityRelationalMap,
            array $ObjectDiscardedRelationships) {
        $RelationalDiscardedRelationships = [];
        foreach($ObjectDiscardedRelationships as $Key => $DiscardedRelationship) {
            if($DiscardedRelationship === null) {
                $RelationalDiscardedRelationships[$Key] = null;
                continue;
            }
            $ParentPrimaryKey = $EntityRelationalMap->MapIdentityToPrimaryKey($DiscardedRelationship->GetParentIdentity());
            $RelatedIdentity = $DiscardedRelationship->GetRelatedIdentity();
            $ChildPrimaryKey = $this->EntityRelationalMaps[$RelatedIdentity->GetEntityType()]->MapIdentityToPrimaryKey($DiscardedRelationship->GetRelatedIdentity());
            
            $RelationalDiscardedRelationships[$Key] = new Relational\DiscardedRelationship(
                    $DiscardedRelationship->IsIdentifying(),
                    $ParentPrimaryKey, 
                    $ChildPrimaryKey);
        }
        
        return $RelationalDiscardedRelationships; 
    }


    /**
     * @return Relational\PersistedRelationship[]
     */
    final public function MapPersistedRelationships(
            Object\UnitOfWork $UnitOfWork,
            Relational\Transaction $Transaction,    
            IEntityRelationalMap $EntityRelationalMap,
            array $ObjectPersistedRelationships) {
        
        $ParentPrimaryKey = null;
        $ChildPersistenceData = [];
        foreach($ObjectPersistedRelationships as $Key => $ObjectPersistedRelationship) {
            if($ObjectPersistedRelationship === null) {
                continue;
            }
            if($ParentPrimaryKey === null) {
                $ParentPrimaryKey = $EntityRelationalMap->MapIdentityToPrimaryKey(
                        $ObjectPersistedRelationship->GetParentIdentity());
            }            
            if ($ObjectPersistedRelationship->IsIdentifying()) {
                $ChildPersistenceData[$Key] = $ObjectPersistedRelationship->GetChildPersistenceData();
            }
        }
        if(count($ChildPersistenceData) > 0) {
            $ChildResultRows = $this->MapPersistenceDataToTransaction($UnitOfWork, $Transaction, $ChildPersistenceData);
        }

        $RelationalPersistedRelationships = [];
        foreach($ObjectPersistedRelationships as $Key => $ObjectPersistedRelationship) {
            if($ObjectPersistedRelationship === null) {
                $RelationalPersistedRelationships[$Key] = null;
                continue;
            }
            
            if ($ObjectPersistedRelationship->IsIdentifying()) {
                $RelationalPersistedRelationships[$Key] = 
                        new Relational\PersistedRelationship($ParentPrimaryKey, null, $ChildResultRows[$Key]);
            }
            else {
                $RelatedIdentity = $ObjectPersistedRelationship->GetRelatedIdentity();
                $RelatedEntityRelationalMap = $this->EntityRelationalMaps[$RelatedIdentity->GetEntityType()];
                $RelatedPrimaryKey = $RelatedEntityRelationalMap->MapIdentityToPrimaryKey($RelatedIdentity);
                $RelationalPersistedRelationships[$Key] = 
                        new Relational\PersistedRelationship($ParentPrimaryKey, $RelatedPrimaryKey, null);
            }
        }
        
        return $RelationalPersistedRelationships;
    }


    /**
     * @internal
     * @return Relational\RelationshipChange
     */
    final public function MapRelationshipChanges(
            Object\UnitOfWork $UnitOfWork, 
            Relational\Transaction $Transaction,
            IEntityRelationalMap $EntityRelationalMap,
            array $ObjectRelationshipChanges) {
        
        $ObjectPersistedRelationships = [];
        $ObjectDiscardedRelationships = [];
        
        foreach($ObjectRelationshipChanges as $Key => $ObjectRelationshipChange) {
            $ObjectPersistedRelationships[$Key] = $ObjectRelationshipChange->GetPersistedRelationship();
            $ObjectDiscardedRelationships[$Key] = $ObjectRelationshipChange->GetDiscardedRelationship();
        }
        
        $RelationalPersistedRelationships = $this->MapPersistedRelationships(
                $UnitOfWork, $Transaction, $EntityRelationalMap, $ObjectPersistedRelationships);
        
        $RelationalDiscardedRelationships = $this->MapDiscardedRelationships($EntityRelationalMap, $ObjectDiscardedRelationships);
        
        $RelationalRelationshipChanges = [];
        foreach($ObjectRelationshipChanges as $Key => $ObjectRelationshipChange) {
            $RelationalRelationshipChanges[$Key] = new Relational\RelationshipChange(
                    $RelationalPersistedRelationships[$Key], $RelationalDiscardedRelationships[$Key]);
        }
        
        return $RelationalRelationshipChanges;
    }

    // </editor-fold>
    
}

?>