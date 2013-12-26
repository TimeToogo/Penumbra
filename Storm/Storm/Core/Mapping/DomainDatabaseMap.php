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
    }
    
    final public function HasRelationMap($EntityType) {
        return isset($this->EntityRelationMaps[$EntityType]);
    }
    
    /**
     * @param string $EntityType
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
     * @return EntityRelationalMap 
     */
    final protected function VerifyRelationMap($EntityType) {
        $RelationMap = $this->GetRelationMap($EntityType);
        if($RelationMap === null)
            throw new \Storm\Core\Exceptions\UnmappedEntityException($EntityType);
        
        return $RelationMap;
    }
    
    final public function Load(Object\IRequest $ObjectRequest) {
        $EntityType = $ObjectRequest->GetEntityType();
        
        $RelationalRequest = $this->MapRequest($ObjectRequest);
        $Rows = $this->Database->Load($RelationalRequest);
        
        $this->ReviveEntities($EntityType, $Rows);
        $Context = new RevivingContext($this);
        $RevivedEntities = $Context->ReviveEntities($EntityType, $Rows);
        
        if($ObjectRequest->IsSingleEntity()) {
            return count($RevivedEntities) > 0 ? reset($RevivedEntities) : null;
        }
        else {
            return $RevivedEntities;
        }
    }
    
    final public function Commit(Object\UnitOfWork $UnitOfWork) {
        $this->EnsureIdentifiable($UnitOfWork->GetPersistedData());
        $this->VerifyIdentifiable($UnitOfWork->GetDiscardedIdentities());
        
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
        $EntityRelationalMap = $this->VerifyRelationMap($ObjectProcedure->GetEntityType());
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
        $EntityRelationalMap = $this->VerifyRelationMap($ObjectRequest->GetEntityType());
        
        $RelationalRequest = new Relational\Request(array(), $ObjectRequest->IsSingleEntity());
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
        
        $PropertyColumnMappings = $EntityRelationalMap->GetPropertyColumnMappings();
        $PropertyRelationMappings = $EntityRelationalMap->GetProperyRelationMappings();
        foreach($Properties as $PropertyIdentifier => $Property) {
            if(isset($PropertyColumnMappings[$PropertyIdentifier])) {
                $PropertyColumnMappings[$PropertyIdentifier]->AddToRelationalRequest($RelationalRequest);
            }
            else if(isset($PropertyRelationMappings[$PropertyIdentifier])) {
                $PropertyRelationMappings[$PropertyIdentifier]->AddToRelationalRequest($this, $RelationalRequest);
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
     * @param Relational\Row[] $Rows
     * @return Object\RevivalData[]
     */
    final public function MapRowsToRevivalData($EntityType, RevivingContext $Context, array $Rows) {
        if (count($Rows) === 0) {
            return array();
        }
        $EntityRelationalMap = $this->EntityRelationMaps[$EntityType];

        $ResultRowRevivalDataMap = new Map();
        $EntityMap = $EntityRelationalMap->GetEntityMap();
        foreach ($Rows as $ResultRow) {
            $ResultRowRevivalDataMap[$ResultRow] = $EntityMap->RevivalData();
        }
        $EntityRelationalMap->Revive($Context, $ResultRowRevivalDataMap);
        $RevivalDataArray = $ResultRowRevivalDataMap->GetToInstances();
        
        return $RevivalDataArray;
    }
    
    /**
     * @internal
     * @param Relational\Row[] $Rows
     * @return object[]
     */
    final public function ReviveEntities($EntityType, RevivingContext $Context, array $Rows) {
        $RevivalDataArray = $this->MapRowsToRevivalData($EntityType, $Context, $Rows);
        return $EntityRelationalMap->GetEntityMap()->ReviveEntities($RevivalDataArray);
    }

    /**
     * @internal
     */
    final public function ReviveEntityInstances(RevivingContext $Context, Map $RowInstanceMap) {
        if (count($RowInstanceMap) === 0)
            return;
        
        $EntityType = null;
        foreach($RowInstanceMap as $Row) {
            $Instance = $RowInstanceMap[$Row];
            $EntityType = get_class($Instance);
            break;
        }
        $EntityRelationalMap = $this->GetRelationMap($EntityType);

        $RowStateMap = new Map();
        $EntityMap = $EntityRelationalMap->GetEntityMap();
        foreach ($RowInstanceMap as $Row) {
            $RowStateMap[$Row] = $EntityMap->State();
        }
        $EntityRelationalMap->Revive($Context, $RowStateMap);
        $StateInstanceMap = new Map();
        foreach ($RowStateMap as $Row) {
            $State = $RowStateMap[$Row];
            $Instance = $RowInstanceMap[$Row];
            $StateInstanceMap[$State] = $Instance;
        }
        return $EntityMap->ReviveEntityInstances($StateInstanceMap);
    }
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Entity Persistence Helpers">

    /**
     * @internal
     * @return Relational\Row[]
     */
    final public function PersistEntity(TransactionalContext $TransactionalContext, $Entity) {
        $State = $this->Domain->Store($Entity);
        return $this->PersistState($TransactionalContext, $State);
    }

    /**
     * @internal
     * @param Relational\Transaction $Transaction
     * @param Object\State $State
     * @return Relational\ColumnData
     */
    final public function PersistState(TransactionalContext $TransactionalContext, Object\State $State) {
        $EntityRelationalMap = $this->VerifyRelationMap($State->GetEntityType());
        $ColumnData = new Relational\ResultRow($EntityRelationalMap->GetAllMappedReviveColumns());
        $Context = new PersistingContext($this, $State, $ColumnData);
        foreach ($EntityRelationalMap->GetPropertyMappings() as $Mapping) {
            $Mapping->Persist($Context, $TransactionalContext);
        }
        $TransactionalContext->GetTransaction()->PersistAll($ColumnData->GetRows());

        return $ColumnData;
    }

    /**
     * @internal
     * @param Relational\Transaction $Transaction
     * @param object $Entity
     * @return Relational\PrimaryKey
     */
    final public function DiscardEntity(TransactionalContext $TransactionalContext, $Entity) {
        $Identity = $this->Domain->Identity($Entity);
        return $this->DiscardIdentity($TransactionalContext, $Identity);
    }


    /**
     * @internal
     * @param Relational\Transaction $Transaction
     * @param Object\Identity $Identity
     * @return Relational\PrimaryKey
     */
    final public function DiscardIdentity(TransactionalContext $TransactionalContext, Object\Identity $Identity) {
        /*$EntityRelationalMap = $this->VerifyRelationMap($Identity->GetEntityType());
        $PrimaryKey = $EntityRelationalMap->GetTable()->PrimaryKey();
        $EntityRelationalMap->MapPropertyDataToColumnData($Identity, $PrimaryKey);
        $Context = new DiscardingContext($this, $Identity, $PrimaryKey);
        
        foreach ($EntityRelationalMap->GetPropertyMappings() as $Mapping) {
            $Mapping->Discard($Context, $TransactionalContext);
        }
        $TransactionalContext->GetTransaction()->Discard($PrimaryKey);

        return $PrimaryKey;*/
    }


    private function MapUnitOfWorkToTransaction(Object\UnitOfWork $UnitOfWork, Relational\Transaction $Transaction) {
        $TransactionalContext = new TransactionalContext($this, $Transaction);
        foreach($UnitOfWork->GeGetPersistedData as $PersistedEntityState) {
            $PersistedRow = $TransactionalContext->PersistState($PersistedEntityState);
            $Transaction->PersistAll($PersistedRow->GetRows());
        }
        foreach($UnitOfWork->GetProcedures() as $Operation) {
            $Transaction->Execute($this->MapProcedure($Operation));
        }
        foreach($UnitOfWork->GetDiscardedIdentities() as $DiscardedIdentity) {
            $DiscardedPrimaryKey = $TransactionalContext->DiscardIdentity($DiscardedIdentity);
            $Transaction->Discard($DiscardedPrimaryKey);
        }
        foreach($UnitOfWork->GetDiscardedRequests() as $DiscardedRequest) {
            $Transaction->DiscardAll($this->MapRequest($DiscardedRequest));
        }
    }
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
                    throw new Exception('Identity properties cannot map to columns across multiple tables');
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