<?php

namespace Storm\Core\Object;

/*
 * TODO: make the order of operations relavent
 */
final class UnitOfWork {
    private $Domain;
    private $PersistenceData = array();
    private $PersistenceDataGroups = array();
    private $Procedures = array();
    private $DiscardedIdentities = array();
    private $DiscardedIdentityGroups = array();
    private $DiscardedRequests = array();
    
    public function __construct(Domain $Domain) {
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
        
        $EntityType = $PersistenceData->GetEntityType();
        if(!isset($this->PersistenceDataGroups[$EntityType])) {
            $this->PersistenceDataGroups[$EntityType] = array();
        }
        $this->PersistenceDataGroups[$EntityType][] = $PersistenceData;
            
        return $PersistenceData;
    }
    
    public function Execute(IProcedure $Procedure) {
        $this->Procedures[] = $Procedure;
    }
    
    /**
     * @return Identity
     */
    public function Discard($Entity) {
        $Hash = spl_object_hash($Entity);
        if(isset($this->DiscardedIdentities[$Hash])) {
            return $this->DiscardedIdentities[$Hash];
        }
        if(!$this->Domain->HasIdentity($Entity)) {
            return null;
        }
        
        $DiscardedIdentity = $this->Domain->Discard($this, $Entity);
        $this->DiscardedIdentities[$Hash] = $DiscardedIdentity;
        
        $EntityType = $DiscardedIdentity->GetEntityType();
        if(!isset($this->DiscardedIdentityGroups[$EntityType])) {
            $this->DiscardedIdentityGroups[$EntityType] = array();
        }
        $this->DiscardedIdentityGroups[$EntityType][] = $PersistenceData;
        
        return $DiscardedIdentity;
    }
    
    public function DiscardWhere(IRequest $Request) {
        $this->DiscardedRequests[spl_object_hash($Request)] = $Request;
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
    public function GetDiscardedIdentities() {
        return $this->DiscardedIdentities;
    }
    
    /**
     * @return PersistenceData[][]
     */
    public function GetDiscardedIdentityGroups() {
        return $this->DiscardedIdentityGroups;
    }
    
    /**
     * @return IRequest[]
     */
    public function GetDiscardedRequests() {
        return $this->DiscardedRequests;
    }
}

?>