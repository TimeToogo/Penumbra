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
     * @var PersistenceData[] 
     */
    private $PersistenceData = [];
    
    /**
     * @var \SplObjectStorage 
     */
    private $PersistedEntities;
    
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
    
    public function __construct(Domain $Domain) {
        $this->Domain = $Domain;
        $this->PersistedEntities = new \SplObjectStorage();
    }
    
    /**
     * @return Domain
     */
    public function GetDomain() {
        return $this->Domain;
    }
    
    private function IsPersisted($Entity) {
        return $this->PersistedEntities->contains($Entity);
    }
    
    private function SetPersisted($Entity, PersistenceData $PersistenceData) {
        $this->PersistedEntities->attach($Entity, $PersistenceData);
    }
    
    private function PersistEntity($Entity) {
        $EntityMap = $this->Domain->VerifyEntity($Entity);
        $PersistenceData = $EntityMap->PersistanceData($Entity);
        $this->SetPersisted($Entity, $PersistenceData);
        
        $EntityMap->Persist($this, $Entity, $PersistenceData);
        
        return $PersistenceData;
    }
    
    /**
     * Persist an entities data and relationships to the unit of work.
     * 
     * @param object $Entity The entity to persist
     * @return void 
     */
    public function PersistRoot($Entity) {
        if($this->IsPersisted($Entity)) {
             return;
        }
        
        $PersistenceData = $this->PersistEntity($Entity);
        
        $this->PersistenceData[] = $PersistenceData;
        
        $EntityType = $PersistenceData->GetEntityType();
        if(!isset($this->PersistenceDataGroups[$EntityType])) {
            $this->PersistenceDataGroups[$EntityType] = [];
        }
        $this->PersistenceDataGroups[$EntityType][] = $PersistenceData;
        
    }
    
    /**
     * Persist an entities data and relationships to and returns the dependent persistence data.
     * 
     * @param object $Entity The entity to persist
     * @return PersistenceData 
     */
    public function PersistDependent($Entity) {
        if($this->IsPersisted($Entity)) {
             return $this->PersistedEntities[$Entity];
        }
        
        $PersistenceData = $this->PersistEntity($Entity);
        
        return $PersistenceData;
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
        
        $this->Domain->VerifyEntity($Entity)->PersistRelationships($this, $Entity);
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
            return $this->DiscardenceData[$Hash]->GetIdentity();
        }
        if(!$this->Domain->HasIdentity($Entity)) {
            return null;
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