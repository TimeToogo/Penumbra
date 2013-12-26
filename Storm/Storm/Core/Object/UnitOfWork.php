<?php

namespace Storm\Core\Object;

/*
 * TODO: make the order of operations relavent
 */
final class UnitOfWork {
    private $Domain;
    private $PersistedData = array();
    private $Procedures = array();
    private $DiscardedIdentities = array();
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
        if(isset($this->PersistedData[$Hash])) {
            return $this->PersistedData[$Hash];
        }
        
        $this->PersistedData[$Hash] = $this->Domain->Persist($this, $Entity);
        return $this->PersistedData[$Hash];
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
        
        $this->DiscardedIdentities[$Hash] = $this->Domain->Identity($Entity);
        $this->Domain->Discard($this, $Entity);
    }
    
    public function DiscardWhere(IRequest $Request) {
        $this->DiscardedRequests[spl_object_hash($Request)] = $Request;
    }
    
    /**
     * @return PersistenceData[]
     */
    public function GetPersistedData() {
        return $this->PersistedData;
    }
    
    /**
     * @return IProcedure[]
     */
    public function GetProcedures() {
        return $this->Procedures;
    }
    
    /**
     * @return Identity[]
     */
    public function GetDiscardedIdentities() {
        return $this->DiscardedIdentities;
    }
    
    /**
     * @return IRequest[]
     */
    public function GetDiscardedRequests() {
        return $this->DiscardedRequests;
    }
}

?>