<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Object;
use \Storm\Core\Relational;
use \Storm\Core\Containers\Registrar;
use \Storm\Core\Containers\Map;

abstract class DomainDatabaseMap {
    /**
     * @var Object\Domain
     */
    private $Domain;
    /**
     * @var Relational\Database
     */
    private $Database;
    /**
     * @var IEntityRelationalMap[] 
     */
    private $EntityRelationMaps = array();
    
    /**
     * @var IEntityRelationalMap[] 
     */
    private $EntityRelationMapsByPrimaryKeyTable = array();
    
    public function __construct() {
        $this->Domain = $this->Domain();
        $this->Database = $this->Database();
        
        $Registrar = new Registrar(IEntityRelationalMap::IEntityRelationalMapType);
        $this->RegisterEntityRelationMaps($Registrar);
        foreach($Registrar->GetRegistered() as $EntityRelationalMap) {
            $this->AddEntityRelationMap($EntityRelationalMap);
        }
    }
    
    protected abstract function Domain();
    protected abstract function Database();
    protected abstract function RegisterEntityRelationMaps(Registrar $Registrar);
    
    /**
     * @return Object\Domain
     */
    final public function GetDomain() {
        return $this->Domain;
    }
    
    /**
     * @return Relational\Database
     */
    final public function GetDatabase() {
        return $this->Database;
    }
        
    final protected function AddEntityRelationMap(IEntityRelationalMap $EntityRelationalMap) {
        $EntityRelationalMap->Initialize($this);
        
        $EntityType = $EntityRelationalMap->GetEntityType();
        if(!$this->Domain->HasEntityMap($EntityType))
            throw new \InvalidArgumentException('$EntityRelationMap must have an EntityMap of this Domain');
        
        $this->EntityRelationMaps[$EntityType] = $EntityRelationalMap;
        $this->EntityRelationMapsByPrimaryKeyTable[$EntityRelationalMap->GetPrimaryKeyTable()->GetName()] = $EntityRelationalMap;
    }
    
    final public function HasRelationMap($EntityType) {
        return isset($this->EntityRelationMaps[$EntityType]);
    }
    
    /**
     * @return IEntityRelationalMap
     */
    final public function GetRelationMap($EntityType) {
        if($this->HasRelationMap($EntityType))
            return $this->EntityRelationMaps[$EntityType];
        else {
            $ParentType = get_parent_class($EntityType);
            if($ParentType === false)
                return null;
            else
                return $this->GetRelationMap($ParentType);
        }
    }
    
    /**
     * @return IEntityRelationalMap
     */
    final public function GetRelationMapByPrimaryKeyTable($TableName) {
        return isset($this->EntityRelationMapsByPrimaryKeyTable[$TableName]) ?
                $this->EntityRelationMapsByPrimaryKeyTable[$TableName] : null;
    }
    
    /**
     * @return IEntityRelationalMap 
     */
    final protected function VerifyRelationalMap($EntityType) {
        $RelationMap = $this->GetRelationMap($EntityType);
        if($RelationMap === null)
            throw new \Storm\Core\Exceptions\UnmappedEntityException($EntityType);
        
        return $RelationMap;
    }
    
    final public function Load(Object\IRequest $ObjectRequest) {
        $EntityType = $ObjectRequest->GetEntityType();
        
        $RelationalRequest = $this->MapRequest($ObjectRequest);
        $Rows = $this->Database->Load($RelationalRequest);
        
        $RevivedEntities = $this->ReviveEntities($EntityType, $Rows);
        
        if($ObjectRequest->IsSingleEntity()) {
            return count($RevivedEntities) > 0 ? reset($RevivedEntities) : null;
        }
        else {
            return $RevivedEntities;
        }
    }
    
    final public function Commit(
            array $EntitiesToPersist,
            array $ProceduresToExecute,
            array $EntitiesToDiscard,
            array $RequestsToDiscard) {
        
        $UnitOfWork = $this->Domain->BuildUnitOfWork(
                $EntitiesToPersist, 
                $ProceduresToExecute, 
                $EntitiesToDiscard, 
                $RequestsToDiscard);
        
        $Transaction = new Relational\Transaction();
        $this->MapUnitOfWorkToTransaction($UnitOfWork, $Transaction);
        
        $this->Database->Commit($Transaction);
    }
    
    // <editor-fold defaultstate="collapsed" desc="Request and Operation mappers">
    /**
     * @param Object\IRequest $ObjectProcedure
     * @return Relational\Procedure
     */
    final public function MapProcedure(Object\IProcedure $ObjectProcedure) {
        $EntityRelationalMap = $this->VerifyRelationalMap($ObjectProcedure->GetEntityType());
        $RelationalProcedure = new Relational\Procedure([$EntityRelationalMap->GetTable()], $ObjectProcedure->IsSingleEntity());
        
        $this->MapRequestData($EntityRelationalMap, $ObjectProcedure, $RelationalProcedure);
        foreach($ObjectProcedure->GetExpressions() as $Expression) {
            $RelationalProcedure->AddExpression($this->MapExpression($EntityRelationalMap, $Expression));
        }
        
        return $RelationalProcedure;
    }
    
    /**
     * @param Object\IRequest $ObjectRequest
     * @return Relational\Request
     */
    final public function MapRequest(Object\IRequest $ObjectRequest) {
        $EntityRelationalMap = $this->VerifyRelationalMap($ObjectRequest->GetEntityType());
        
        $RelationalRequest = $EntityRelationalMap->RelationalRequest($ObjectRequest);
        $this->MapPropetiesToRelationalRequest($EntityRelationalMap, $RelationalRequest, $ObjectRequest->GetProperties());
        
        $this->MapRequestData($EntityRelationalMap, $ObjectRequest, $RelationalRequest);
        

        return $RelationalRequest;
    }
    
    /**
     * @internal
     */
    final public function MapEntityToRelationalRequest($EntityType, Relational\Request $RelationalRequest) {
        $this->MapPropetiesToRelationalRequest($this->EntityRelationMaps[$EntityType], $RelationalRequest);
    }
    
    private function MapPropetiesToRelationalRequest(IEntityRelationalMap $EntityRelationalMap, Relational\Request $RelationalRequest, array $Properties = null) {
        if($Properties === null) {
            $Properties = $EntityRelationalMap->GetEntityMap()->GetProperties();
        }
        
        $DataPropertyColumnMappings = $EntityRelationalMap->GetDataPropertyColumnMappings();
        $EntityPropertyToOneRelationMappings = $EntityRelationalMap->GetEntityPropertyToOneRelationMappings();
        $CollectionPropertyToManyRelationMappings = $EntityRelationalMap->GetCollectionPropertyToManyRelationMappings();
        foreach($Properties as $PropertyIdentifier => $Property) {
            if(isset($DataPropertyColumnMappings[$PropertyIdentifier])) {
                $RelationalRequest->AddColumns($DataPropertyColumnMappings[$PropertyIdentifier]->GetReviveColumns());
            }
            else if(isset($EntityPropertyToOneRelationMappings[$PropertyIdentifier])) {
                $EntityPropertyToOneRelationMappings[$PropertyIdentifier]->AddToRelationalRequest($this, $RelationalRequest);
            }
            else if(isset($CollectionPropertyToManyRelationMappings[$PropertyIdentifier])) {
                $CollectionPropertyToManyRelationMappings[$PropertyIdentifier]->AddToRelationalRequest($this, $RelationalRequest);
            }
            else {
                throw new \Storm\Core\Exceptions\UnmappedPropertyException();
            }
        }
    }


    private function MapRequestData(IEntityRelationalMap $EntityRelationalMap,
            Object\IRequest $ObjectRequest, Relational\Request $RelationalRequest) {
        if ($ObjectRequest->IsConstrained()) {
            foreach ($this->MapPredicates($EntityRelationalMap, $ObjectRequest->GetPredicates()) as $Property) {
                $RelationalRequest->AddPredicate($Property);
            }
        }
        if ($ObjectRequest->IsOrdered()) {
            $PropertyAscendingMap = $ObjectRequest->GetOrderedPropertiesAscendingMap();
            foreach ($PropertyAscendingMap as $Property) {
                $Ascending = $PropertyAscendingMap[$Property];
                $Columns = $EntityRelationalMap->GetMappedColumns($Property);
                foreach($Columns as $Column) {
                    $RelationalRequest->AddOrderByColumn($Column, $Ascending);
                }
            }
        }
        if ($ObjectRequest->IsRanged()) {
            $RelationalRequest->SetRangeOffset($ObjectRequest->GetRangeOffset());
            $RelationalRequest->SetRangeAmount($ObjectRequest->GetRangeAmount());
        }
    }
    
    /**
     * @param Object\Constraints\IPredicate[] $Predicates
     * @return Relational\Constraints\Predicate[]
     */
    final public function MapPredicates(IEntityRelationalMap $EntityRelationalMap, array $Predicates) {
        if (count($Predicates) === 0)
            return array();
        
        $RelationalPredicates = array();
        foreach ($Predicates as $Predicate) {
            $RelationalPredicates[] = $this->MapPredicate($EntityRelationalMap, $Predicate);
        }
        
        return $RelationalPredicates;
    }

    final public function MapPredicate(IEntityRelationalMap $EntityRelationalMap, Object\Constraints\Predicate $Predicate) {
        $RelationalPredicate = new Relational\Constraints\Predicate();
        
        foreach ($Predicate->GetRuleGroups() as $RuleGroup) {
            $RelationalPredicate->AddRules($this->MapRuleGroup($EntityRelationalMap, $RuleGroup));
        }
        
        return $RelationalPredicate;
    }

    private function MapRuleGroup(IEntityRelationalMap $EntityRelationalMap, Object\Constraints\RuleGroup $RuleGroup) {
        $RelationalRuleGroup = new Relational\Constraints\RuleGroup(array(), $RuleGroup->IsAllRequired());

        foreach ($RuleGroup->GetRules() as $Rule) {
            $RelationalRuleGroup->AddRule($this->MapRule($EntityRelationalMap, $Rule));
        }
        foreach ($RuleGroup->GetRuleGroups() as $RuleGroup) {
            $RelationalRuleGroup->AddRuleGroup($this->MapRuleGroup($EntityRelationalMap, $RuleGroup));
        }

        return $RelationalRuleGroup;
    }

    private function MapRule(IEntityRelationalMap $EntityRelationalMap, Object\Constraints\IRule $Rule) {
        return new Relational\Constraints\Rule
                ($this->MapExpression($EntityRelationalMap, $Rule->GetExpression()));
    }
    
    /**
     * @return Relational\Expressions\Expression
     */
    protected abstract function MapExpression(IEntityRelationalMap $EntityRelationalMap, Object\Expressions\Expression $Expression);
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Entity Revival Helpers">
    /**
     * @internal
     * @return object[]
     */
    final public function ReviveEntities($EntityType, array $ResultRows) {
        $RevivalDataArray = $this->MapRowsToRevivalData($EntityType, $ResultRows);
        return $this->Domain->ReviveEntities($EntityType, $RevivalDataArray);
    }
    
    /**
     * @internal
     * @return Object\RevivalData[]
     */
    final public function MapRowsToRevivalData($EntityType, array $ResultRows) {
        if (count($ResultRows) === 0) {
            return array();
        }
        
        $EntityRelationalMap = $this->VerifyRelationalMap($EntityType);

        $ResultRowRevivalDataMap = new Map();
        $RevivalDataArray = array();
        $EntityMap = $EntityRelationalMap->GetEntityMap();
        foreach ($ResultRows as $Key => $ResultRow) {
            $RevivalData = $EntityMap->RevivalData();
            $ResultRowRevivalDataMap[$ResultRow] = $RevivalData;
            $RevivalDataArray[$Key] = $RevivalData;
        }
        
        $this->MapResultRowsToRevivalData($EntityRelationalMap, $ResultRowRevivalDataMap);
        
        return $RevivalDataArray;
    }
    
    final public function MapResultRowsToRevivalData(IEntityRelationalMap $EntityRelationalMap, Map $ResultRowRevivalDataMap) {
        foreach($EntityRelationalMap->GetDataPropertyColumnMappings() as $PropertyColumnMapping) {
            $PropertyColumnMapping->Revive($ResultRowRevivalDataMap);
        }
        foreach($EntityRelationalMap->GetEntityPropertyToOneRelationMappings() as $EntityPropertyToOneRelationMapping) {
            $EntityPropertyToOneRelationMapping->Revive($this, $ResultRowRevivalDataMap);
        }
        foreach($EntityRelationalMap->GetCollectionPropertyToManyRelationMappings() as $CollectionPropertyToManyRelationMapping) {
            $CollectionPropertyToManyRelationMapping->Revive($this, $ResultRowRevivalDataMap);
        }
    }
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Entity Persistence Helpers">

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
            $EntityRelationalMap = $this->EntityRelationMaps[$EntityType];
            $ResultRows = $this->MapEntityDataToTransaction($UnitOfWork, $Transaction, $EntityRelationalMap, $DiscardedIdentityGroup);
            foreach($ResultRows as $ResultRow) {
                $Transaction->DiscardAll($ResultRow->GetPrimaryKeys());
            }            
        }
        foreach($UnitOfWork->GetDiscardedRequests() as $DiscardedRequest) {
            $Transaction->DiscardWhere($this->MapRequest($DiscardedRequest));
        }
    }
    
    private function MapPersistenceDataToTransaction(
            Object\UnitOfWork $UnitOfWork, 
            Relational\Transaction $Transaction,
            array $PersistenceDataArray) {
        if(count($PersistenceDataArray) === 0) {
            return;
        }
        
        $EntityRelationalMap = $this->EntityRelationMaps[reset($PersistenceDataArray)->GetEntityType()];
        $PrimaryKeyTable = $EntityRelationalMap->GetPrimaryKeyTable();
        $ResultRows = $this->MapEntityDataToTransaction($UnitOfWork, $Transaction, $EntityRelationalMap, $PersistenceDataArray);
        
        foreach($ResultRows as $Key => $ResultRow) {
            $PersistenceData = $PersistenceDataArray[$Key];
            $Transaction->PersistAll($ResultRow->GetRows());
            
            $PrimaryKeyRow = $ResultRow->GetRow($PrimaryKeyTable);
            if(!$PrimaryKeyRow->HasPrimaryKey()) {
                $Transaction->SubscribeToPostPersistEvent(
                        $ResultRow->GetRow($PrimaryKeyTable), 
                        function ($PersistedRow) use (&$UnitOfWork, $PersistenceData, &$EntityRelationalMap) {
                            $Identity = $EntityRelationalMap->MapPrimaryKeyToIdentity($PersistedRow->GetPrimaryKey());
                            $UnitOfWork->SupplyIdentity($PersistenceData, $Identity);
                        });
            }
        }
        
        return $ResultRows;
    }
    
    private function MapEntityDataToTransaction(
            Object\UnitOfWork $UnitOfWork, Relational\Transaction $Transaction, 
            IEntityRelationalMap $EntityRelationalMap, array $EntityDataArray) {
        
        $DataPropertyColumnMappings = $EntityRelationalMap->GetDataPropertyColumnMappings();
        $EntityPropertyToOneRelationMappings = $EntityRelationalMap->GetEntityPropertyToOneRelationMappings();
        $CollectionPropertyToManyRelationMappings = $EntityRelationalMap->GetCollectionPropertyToManyRelationMappings();
        
        $ResultRowArray = array();
        foreach($EntityDataArray as $Key => $EntityData) {
            $ResultRowData = $EntityRelationalMap->ResultRow();
            
            foreach($DataPropertyColumnMappings as $DataPropertyColumnMapping) {
                $Property = $DataPropertyColumnMapping->GetProperty();
                if(isset($EntityData[$Property])) {
                    $DataPropertyValue = $EntityData[$Property];
                    $DataPropertyColumnMapping->Persist($DataPropertyValue, $ResultRowData);
                }
            }
            
            foreach($EntityPropertyToOneRelationMappings as $EntityPropertyToOneRelationMapping) {
                $RelationshipChange = $EntityData[$EntityPropertyToOneRelationMapping->GetProperty()];
                $MappedRelationshipChange = 
                        $this->MapRelationshipChanges($UnitOfWork, $Transaction, 
                        [$RelationshipChange])[0];
                $EntityPropertyToOneRelationMapping->Persist($Transaction, $ResultRowData, $MappedRelationshipChange);
            }
            
            foreach($CollectionPropertyToManyRelationMappings as $CollectionPropertyToManyRelationMapping) {
                $RelationshipChanges = $EntityData[$CollectionPropertyToManyRelationMapping->GetProperty()];
                $MappedRelationshipChanges = 
                        $this->MapRelationshipChanges($UnitOfWork, $Transaction, $RelationshipChanges);
                
                $CollectionPropertyToManyRelationMapping->Persist($Transaction, $ResultRowData, $MappedRelationshipChanges);
            }
            
            $ResultRowArray[$Key] = $ResultRowData;
        }
        
        return $ResultRowArray;
    }
    
    // <editor-fold defaultstate="collapsed" desc="Relationship Mapping Helpers">
    
    private function MapIdentityToPrimaryKey(Object\Identity $Identity) {
        $EntityRelationalMap = $this->VerifyRelationalMap($Identity->GetEntityType());
        return $EntityRelationalMap->MapIdentityToPrimaryKey($Identity);
    }
    
    private function MapPrimaryKeyToIdentity(Relational\PrimaryKey $PrimaryKey) {
        $EntityRelationalMap = $this->GetRelationMapByPrimaryKeyTable($PrimaryKey->GetTable()->GetName());
        return $EntityRelationalMap->MapPrimaryKeyToIdentity($PrimaryKey);
    }
    
    /**
     * @internal
     * @return Relational\DiscardedRelationship
     */
    final public function MapDiscardedRelationships(array $ObjectDiscardedRelationships) {
        $RelationalDiscardedRelationships = array();
        foreach($ObjectDiscardedRelationships as $Key => $DiscardedRelationship) {
            if($DiscardedRelationship === null) {
                $RelationalDiscardedRelationships[$Key] = null;
                continue;
            }
            $ParentPrimaryKey = $this->MapIdentityToPrimaryKey($DiscardedRelationship->GetParentIdentity());
            $ChildPrimaryKey = $this->MapIdentityToPrimaryKey($DiscardedRelationship->GetRelatedIdentity());
            
            $RelationalDiscardedRelationships[$Key] = new Relational\DiscardedRelationship($ParentPrimaryKey, $ChildPrimaryKey);
        }
        
        return $RelationalDiscardedRelationships; 
    }


    /**
     * @internal
     * @return Relational\PersistedRelationship
     */
    final public function MapPersistedRelationships(
            Object\UnitOfWork $UnitOfWork, Relational\Transaction $Transaction,             
            array $ObjectPersistedRelationships) {
        
        $ChildPersistenceData = array();
        foreach($ObjectPersistedRelationships as $Key => $ObjectPersistedRelationship) {
            if($ObjectPersistedRelationship === null) {
                continue;
            }
            if ($ObjectPersistedRelationship->IsIdentifying()) {
                $ChildPersistenceData[$Key] = $ObjectPersistedRelationship->GetChildPersistenceData();
            }
        }
        $ChildResultRows = $this->MapPersistenceDataToTransaction($UnitOfWork, $Transaction, $ChildPersistenceData);
        
        $ParentPrimaryKey = $this->MapIdentityToPrimaryKey($ObjectPersistedRelationship->GetParentIdentity());

        $RelationalPersistedRelationships = array();
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
                $RelatedPrimaryKey = $this->MapIdentityToPrimaryKey($ObjectPersistedRelationship->GetRelatedIdentity());
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
            Object\UnitOfWork $UnitOfWork, Relational\Transaction $Transaction,
            array $ObjectRelationshipChanges) {
        $ObjectPersistedRelationships = array();
        $ObjectDiscardedRelationships = array();
        foreach($ObjectRelationshipChanges as $Key => $ObjectRelationshipChange) {
            $ObjectPersistedRelationships[$Key] = $ObjectRelationshipChange->GetPersistedRelationship();
            $ObjectDiscardedRelationships[$Key] = $ObjectRelationshipChange->GetDiscardedRelationship();
            if ($ObjectRelationshipChange->HasDiscardedRelationship()) {
                $ObjectPersistedRelationships[$Key] = $this->MapDiscardedRelationship($ObjectRelationshipChange->GetDiscardedRelationship());
            }
        }
        $RelationalPersistedRelationships = $this->MapPersistedRelationships($UnitOfWork, $Transaction, 
                $ObjectPersistedRelationships);
        $RelationalDiscardedRelationships = $this->MapDiscardedRelationships($ObjectDiscardedRelationships);
        
        $RelationalRelationshipChanges = array();
        foreach($ObjectRelationshipChanges as $Key => $ObjectRelationshipChange) {
            $RelationalRelationshipChanges[$Key] = new Relational\RelationshipChange(
                    $RelationalPersistedRelationships[$Key], $RelationalDiscardedRelationships[$Key]);
        }
        
        return $RelationalRelationshipChanges;
    }

    // </editor-fold>
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Entity Identification Helpers">
    final public function EnsureIdentifiable(array $Entities) {
        if(count($Entities) === 0)
            return;
        
        $EntityTypes = array_unique(array_map('get_class', $Entities));
        foreach ($EntityTypes as $EntityType) {
            $EntitiesOfType = array_filter($Entities, 
                    function ($Entity) use($EntityType) 
                    {
                        return $Entity instanceof $EntityType;
                    });
            
            $EntityRelationalMap = $this->GetRelationMap($EntityType);
            $EntityMap = $EntityRelationalMap->GetEntityMap();
            
            $IdentityProperties = $EntityMap->GetIdentityProperties();
            $PrimaryKeyColumns = $EntityRelationalMap->GetAllMappedReviveColumns($IdentityProperties);
            $Table = null;
            foreach($PrimaryKeyColumns as $Column) {
                if($Table === null) {
                    $Table = $Column->GetTable();
                }
                else if(!$Column->GetTable()->Is($Table)) {
                    throw new \Exception('Identity properties cannot map to columns across multiple tables');
                }
            }
            
            $UnidentifiableEntities = $this->GetUnidentifiable($EntityMap, $EntitiesOfType);
            
            $NewPrimaryKeys = $this->Database->GeneratePrimaryKeys($Table, count($UnidentifiableEntities));
            
            $NewIdentities = array_fill_keys(array_keys($NewPrimaryKeys), null);
            array_walk($NewIdentities, 
                    function (&$Value, $Key) use (&$EntityMap) {
                        $Value = $EntityMap->Identity();
                    });
                    
            array_walk($NewPrimaryKeys, 
                    function ($PrimaryKey, $Key) use (&$NewIdentities, &$EntityRelationalMap) {
                        $EntityRelationalMap->MapColumnDataToPropertyData($PrimaryKey, $NewIdentities[$Key]);
                    });


            $Count = 0;
            $NewIdentities = array_values($NewIdentities);
            foreach ($UnidentifiableEntities as $Entity) {
                $EntityMap->Apply($Entity, $NewIdentities[$Count]);
                $Count++;
            }
        }
    
    }
    
    private function GetUnidentifiable(Object\EntityMap $EntityMap, array $Entities) {
        $UnidentifiableEntities = array();
        foreach ($Entities as $Entity) {
            if (!$EntityMap->HasIdentity($Entity)) {
                $UnidentifiableEntities[] = $Entity;
            }
        }
        
        return $UnidentifiableEntities;
    }

    final public function IsIdenitifiable($Entity) {
        return $this->Domain->HasIdentity($Entity);
    }
    
    final public function VerifyIdentifiable(array $Entities) {
        if (count(array_filter($Entities, [$this, 'IsIdenitifiable'])) !== count($Entities))
            throw new \Storm\Core\Exceptions\UnidentifiableEntityException();
    }
    // </editor-fold>
}

?>