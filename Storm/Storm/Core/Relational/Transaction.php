<?php

namespace Storm\Core\Relational;

use Storm\Core\Containers\Map;

final class Transaction {
    private $PersistedRows = array();
    private $PersistedRowGroups = array();
    private $PostPersistRowEventMap;
    private $Procedures = array();
    private $DiscardedPrimaryKeys = array();
    private $DiscardedPrimaryKeyGroups = array();
    private $DiscardedRequests = array();
    
    public function __construct() {
        $this->PostPersistRowEventMap = new Map;
    }
    
    /**
     * @return Row[]
     */
    public function GetPersistedRows() {
        return $this->PersistedRows;
    }
    
    /**
     * @return Row[][]
     */
    public function GetPersistedRowGroups() {
        return $this->PersistedRowGroups;
    }
    
    /**
     * @return Procedure[]
     */
    public function GetProcedures() {
        return $this->Procedures;
    }
    
    /**
     * @return PrimaryKey[]
     */
    public function GetDiscardedPrimaryKeys() {
        return $this->DiscardedPrimaryKeys;
    }
    
    /**
     * @return PrimaryKey[][]
     */
    public function GetDiscardedPrimaryKeyGroups() {
        return $this->DiscardedPrimaryKeyGroups;
    }
    
    /**
     * @return Request[]
     */
    public function GetDiscardedRequests() {
        return $this->DiscardedRequests;
    }
    
    public function Persist(Row $Row) {
        $this->PersistedRows[spl_object_hash($Row)] = $Row;
        if(!isset($this->PostPersistRowEvent[$Row])) {
            $this->PostPersistRowEvent[$Row] = new \ArrayObject();
        }
        
        $TableName = $Row->GetTable()->GetName();
        if(!isset($this->PersistedRowGroups[$TableName])) {
            $this->PersistedRowGroups[$TableName] = array();
        }
        $this->PersistedRowGroups[$TableName][] = $Row;
    }
    
    public function PersistAll(array $Rows) {
        array_walk($Rows, [$this, 'Persist']);
    }
    
    public function TriggerPostPersistEvent(array $Rows) {
        foreach($Rows as $Row) {
            $Events = $this->PostPersistRowEventMap[$Row];
            foreach($Events as $Event) {
                $Event($Row);
            }
        }
    }
    
    public function SubscribeToPostPersistEvent(Row $Row, callable $Event) {
        $this->PostPersistRowEvent[$Row][] = $Event;
    }
    
    public function Execute(Procedure $Procedure) {
        $this->Procedures[] = $Procedure;
    }
    
    public function Discard(PrimaryKey $PrimaryKey) {
        $this->DiscardedPrimaryKeys[] = $PrimaryKey;
        
        $TableName = $PrimaryKey->GetTable()->GetName();
        if(!isset($this->DiscardedPrimaryKeyGroups[$TableName])) {
            $this->DiscardedPrimaryKeyGroups[$TableName] = array();
        }
        $this->DiscardedPrimaryKeyGroups[$TableName][] = $PrimaryKey;
    }
    
    public function DiscardAll(Request $Request) {
        $this->DiscardedRequests[] = $Request;
    }
}

?>