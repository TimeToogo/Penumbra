<?php

namespace Storm\Core\Relational;

final class Transaction {
    private $PersistedRows = array();
    private $Operations = array();
    private $DiscardedPrimaryKeys = array();
    private $DiscardedRequests = array();
    
    public function __construct() {
    }
    
    /**
     * @return Row[]
     */
    public function GetPersistedRows() {
        return $this->PersistedRows;
    }
    
    /**
     * @return Operation[]
     */
    public function GetOperations() {
        return $this->Operations;
    }
    
    /**
     * @return PrimaryKey[]
     */
    public function GetDiscardedPrimaryKeys() {
        return $this->DiscardedPrimaryKeys;
    }
    
    /**
     * @return Request[]
     */
    public function GetDiscardedRequests() {
        return $this->DiscardedRequests;
    }
    
    public function Persist(Row $Row) {
        $this->PersistedRows[] = $Row;
    }
    
    public function PersistAll(array $Rows) {
        array_walk($Rows, [$this, 'Persist']);
    }
    
    public function Execute(Operation $Operation) {
        $this->Operations[] = $Operation;
    }
    
    public function Discard(PrimaryKey $PrimaryKey) {
        $this->DiscardedPrimaryKeys[] = $PrimaryKey;
    }
    
    public function DiscardAll(Request $Request) {
        $this->DiscardedRequests[] = $Request;
    }
}

?>