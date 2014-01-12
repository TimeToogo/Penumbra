<?php

namespace Storm\Core\Object;

/*
 * TODO: make the order of operations relavent
 */
final class UnitOfWork {
    private $Domain;
    private $PersistenceDataEntityMap;
    private $PersistenceData = array();
    private $PersistenceDataGroups = array();
    private $Procedures = array();
    private $DiscardenceData = array();
    private $DiscardedIdentityGroups = array();
    private $DiscardedCriteria = array();
    
    public function __construct(Domain $Domain) {
        $this->PersistenceDataEntityMap = new \Storm\Core\Containers\Map();
        $this->Domain = $Domain;
    }
    
    /**
     * @return Domain
     */
    public function GetDomain() {
        return $this->Domain;
    }
    
    /**
     * @return PersistanceData
     */
    public function Persist($Entity) {
        $Hash = spl_object_hash($Entity);
        if(isset($this->PersistenceData[$Hash])) {
            return $this->PersistenceData[$Hash];
        }
        
        $PersistenceData = $this->Domain->Persist($this, $Entity);
        $this->PersistenceData[$Hash] = $PersistenceData;
        $this->PersistenceDataEntityMap[$PersistenceData] = $Entity;
        
        $EntityType = $PersistenceData->GetEntityType();
        if(!isset($this->PersistenceDataGroups[$EntityType])) {
            $this->PersistenceDataGroups[$EntityType] = array();
        }
        $this->PersistenceDataGroups[$EntityType][] = $PersistenceData;
            
        return $PersistenceData;
    }
    
    public function SupplyIdentity(PersistenceData $PersistenceData, Identity $Identity) {
        if(isset($this->PersistenceDataEntityMap[$PersistenceData])) {
            $Entity = $this->PersistenceDataEntityMap[$PersistenceData];
            $this->Domain->Apply($Entity, $Identity);
        }
    }
    
    public function GetEntities() {
        return $this->PersistenceDataEntityMap->GetToInstances();
    }
    
    public function Execute(IProcedure $Procedure) {
        $this->Procedures[] = $Procedure;
    }
    
    /**
     * @return DiscardenceData
     */
    public function Discard($Entity) {
        $Hash = spl_object_hash($Entity);
        if(isset($this->DiscardenceData[$Hash])) {
            return $this->DiscardenceData[$Hash];
        }
        if(!$this->Domain->HasIdentity($Entity)) {
            return null;
        }
        
        $DiscardenceData = $this->Domain->Discard($this, $Entity);
        $this->DiscardenceData[$Hash] = $DiscardenceData;
        
        $EntityType = $DiscardenceData->GetEntityType();
        if(!isset($this->DiscardedIdentityGroups[$EntityType])) {
            $this->DiscardedIdentityGroups[$EntityType] = array();
        }
        $this->DiscardedIdentityGroups[$EntityType][] = $DiscardenceData;
        
        return $DiscardenceData;
    }
    
    public function DiscardWhere(ICriterion $Criterion) {
        $this->DiscardedCriteria[spl_object_hash($Criterion)] = $Criterion;
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
        return $this->Procedures;
    }
    
    /**
     * @return PersistenceData[]
     */
    public function GetDiscardenceData() {
        return $this->DiscardenceData;
    }
    
    /**
     * @return PersistenceData[][]
     */
    public function GetDiscardenceDataGroups() {
        return $this->DiscardedIdentityGroups;
    }
    
    /**
     * @return ICriterion[]
     */
    public function GetDiscardedCriteria() {
        return $this->DiscardedCriteria;
    }
}

?>