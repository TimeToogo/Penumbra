<?php

namespace Storm\Core\Object;

/*
 * TODO: make the order of operations relavent
 */
final class UnitOfWork {
    private $Domain;
    private $PersistedStates = array();
    private $Procedures = array();
    private $DiscardedIdentities = array();
    private $DiscardedRequests = array();
    
    public function __construct(Domain $Domain) {
        $this->Domain = $Domain;
    }
    
    public function Persist($Entity) {
        $Hash = spl_object_hash($Entity);
        if(isset($this->PersistedStates[$Hash]))
            return;
        
        $this->PersistedStates[$Hash] = $this->Domain->State($Entity);
        $this->Domain->Persist($Entity, $this);
    }
    public function Execute(IProcedure $Procedure) {
        $this->Procedures[] = $Procedure;
    }
    public function Discard($Entity) {
        $Hash = spl_object_hash($Entity);
        if(isset($this->DiscardedIdentities[$Hash]))
            return;
        
        $this->DiscardedIdentities[spl_object_hash($Entity)] = $this->Domain->Identity($Entity);
        $this->Domain->Discard($Entity, $this);
    }
    public function DiscardWhere(IRequest $Request) {
        $this->DiscardedRequests[spl_object_hash($Request)] = $Request;
    }
    
    /**
     * @return State[]
     */
    public function GetPersistedStates() {
        return $this->PersistedStates;
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