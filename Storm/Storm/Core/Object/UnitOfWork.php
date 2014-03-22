<?php

namespace Storm\Core\Object;

/**
 * This unit of work contains data for taking actions against entities in a given domain.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class UnitOfWork {
    /**
     * @var Domain 
     */
    private $Domain;
    
    /**
     * @var Map 
     */
    private $PersistenceDataEntityMap;
    
    /**
     * @var PersistenceData[] 
     */
    private $PersistenceData = [];
    
    /**
     * @var PersistenceData[][]
     */
    private $PersistenceDataGroups = [];
    
    /**
     * @var IProcedure[] 
     */
    private $ProcedureToExecute = [];
    
    /**
     * @var DiscardenceData[] 
     */
    private $DiscardenceData = [];
    
    /**
     * @var DiscardenceData[][] 
     */
    private $DiscardenceDataGroups = [];
    
    /**
     * @var ICriteria[] 
     */
    private $DiscardedCriteria = [];
    
    /**
     * @var \SplObjectStorage 
     */
    private $PersistedEntityIdentityMap;
    
    public function __construct(Domain $Domain) {
        $this->Domain = $Domain;
        $this->PersistedEntityIdentityMap = new \SplObjectStorage();
    }
    
    /**
     * @return Domain
     */
    public function GetDomain() {
        return $this->Domain;
    }
    
    public function PersistedIdentity($Entity) {
        if(!isset($this->PersistedEntityIdentityMap[$Entity])) {
            return null;
        }        
        
        return $this->PersistedEntityIdentityMap[$Entity];
    }
    
    /**
     * Persist an entities data and relationships to the unit of work.
     * 
     * @param object $Entity The entity to persist
     * @return Identity 
     */
    public function Persist($Entity) {
        $Hash = spl_object_hash($Entity);
        if(isset($this->PersistenceData[$Hash])) {
             return;
        }
        
        $PersistenceData = $this->Domain->Persist($this, $Entity);
        $this->PersistenceData[$Hash] = $PersistenceData;
        $this->PersistedEntityIdentityMap[$Entity] = $PersistenceData->GetIdentity();
        
        $EntityType = $PersistenceData->GetEntityType();
        if(!isset($this->PersistenceDataGroups[$EntityType])) {
            $this->PersistenceDataGroups[$EntityType] = [];
        }
        $this->PersistenceDataGroups[$EntityType][] = $PersistenceData;
        
        return $PersistenceData->GetIdentity();
    }
    
    /**
     * Persist an entity's relationships to the unit of work.
     * 
     * @param object $Entity The entity to persist
     * @return void 
     */
    public function PersistRelationships($Entity) {
        $Hash = spl_object_hash($Entity);
        if(isset($this->PersistenceData[$Hash])) {
             return;
        }
        
        $this->Domain->PersistRelationships($this, $Entity);
    }
    
    /**
     * Sets a generated identity to to the entity mapped with the supplied persistence data.
     * 
     * @param PersistenceData $PersistenceData The persistence data of the entity
     * @param Identity $Identity The identity to supply the entity
     */
    public function SupplyIdentity(PersistenceData $PersistenceData, Identity $Identity) {
        if(isset($this->PersistenceDataEntityMap[$PersistenceData])) {
            $Entity = $this->PersistenceDataEntityMap[$PersistenceData];
            $this->Domain->Apply($Entity, $Identity);
        }
    }
    
    private function TypeMismatch($ObjectName, $EntityType) {
        return new TypeMismatchException(
                'The supplied %s of entity %s is part of the given domain %s',
                $ObjectName,
                $EntityType,
                $this->Domain->GetType());
    }
    
    /**
     * Add a procedure to be executed.
     * 
     * @param IProcedure $Procedure The procedure to execute
     * @return void
     */
    public function Execute(IProcedure $Procedure) {
        if(!$this->Domain->HasEntityMap($Procedure->GetEntityType())) {
            throw $this->TypeMismatch('procedure', $Procedure->GetEntityType());
        }
        $this->ProcedureToExecute[] = $Procedure;
    }
    
    /**
     * Discards the supplied entity 
     * 
     * @param object $Entity The entity to discard
     * @return Identity
     */
    public function Discard($Entity) {
        $Hash = spl_object_hash($Entity);
        if(isset($this->DiscardenceData[$Hash])) {
            return $this->DiscardenceData[$Hash];
        }
        if(!$this->Domain->HasIdentity($Entity)) {
            return;
        }
        
        $DiscardenceData = $this->Domain->Discard($this, $Entity);
        $this->DiscardenceData[$Hash] = $DiscardenceData;
        
        $EntityType = $DiscardenceData->GetEntityType();
        if(!isset($this->DiscardenceDataGroups[$EntityType])) {
            $this->DiscardenceDataGroups[$EntityType] = [];
        }
        $this->DiscardenceDataGroups[$EntityType][] = $DiscardenceData;
        
        return $DiscardenceData->GetIdentity();
    }
    
    /**
     * Discard all entities matching the supplied criteria.
     * 
     * @param ICriteria $Criteria The criteria to discard by
     */
    public function DiscardBy(ICriteria $Criteria) {
        if(!$this->Domain->HasEntityMap($Criteria->GetEntityType())) {
            throw $this->TypeMismatch('criteria', $Criteria->GetEntityType());
        }
        $this->DiscardedCriteria[spl_object_hash($Criteria)] = $Criteria;
    }
    
    /**
     * @return PersistenceData[]
     */
    public function GetPersistenceData() {
        return $this->PersistenceData;
    }
    
    /**
     * @return PersistenceData[][]
     */
    public function GetPersistenceDataGroups() {
        return $this->PersistenceDataGroups;
    }
    
    /**
     * @return IProcedure[]
     */
    public function GetExecutedProcedures() {
        return $this->ProcedureToExecute;
    }
    
    /**
     * @return DiscardenceData[]
     */
    public function GetDiscardenceData() {
        return $this->DiscardenceData;
    }
    
    /**
     * @return DiscardenceData[][]
     */
    public function GetDiscardenceDataGroups() {
        return $this->DiscardenceDataGroups;
    }
    
    /**
     * @return ICriteria[]
     */
    public function GetDiscardedCriteria() {
        return $this->DiscardedCriteria;
    }
}

?>