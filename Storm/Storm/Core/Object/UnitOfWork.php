<?php

namespace Storm\Core\Object;

final class UnitOfWork {
    private $PersistedEntityStates = array();
    private $Operations = array();
    private $DiscardedIdentities = array();
    private $DiscardedRequests = array();
    
    public function __construct() {
    }
    
    public function Persist(State $EntityState) {
        $this->PersistedEntityStates[] = $EntityState;
    }
    public function Execute(IOperation $Operation) {
        $this->Operations[] = $Operation;
    }
    public function Discard(Identity $Identity) {
        $this->DiscardedIdentities[] = $Identity;
    }
    public function DiscardWhere(IRequest $Request) {
        $this->DiscardedRequests[] = $Identity;
    }
    
    /**
     * @return State[]
     */
    public function GetPersistedEntityStates() {
        return $this->PersistedEntityStates;
    }
    
    /**
     * @return IOperation[]
     */
    public function GetOperations() {
        return $this->Operations;
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