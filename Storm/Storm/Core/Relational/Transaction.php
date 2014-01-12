<?php

namespace Storm\Core\Relational;

use Storm\Core\Containers\Map;

final class Transaction {
    private $PersistedRows = array();
    private $PersistedRowGroups = array();
    private $PrePersistRowEventMap;
    private $PostPersistRowEventMap;
    private $Procedures = array();
    private $DiscardedPrimaryKeys = array();
    private $DiscardedPrimaryKeyGroups = array();
    private $DiscardedCriteria = array();
    
    public function __construct() {
        $this->PrePersistRowEventMap = new Map;
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
     * @return Criterion[]
     */
    public function GetDiscardedCriteria() {
        return $this->DiscardedCriteria;
    }
    
    public function Persist(Row $Row) {
        $Hash = spl_object_hash($Row);
        if(isset($this->PersistedRows[$Hash])) {
            return;
        }
        $this->PersistedRows[$Hash] = $Row;
        
        $TableName = $Row->GetTable()->GetName();
        if(!isset($this->PersistedRowGroups[$TableName])) {
            $this->PersistedRowGroups[$TableName] = array();
        }
        $this->PersistedRowGroups[$TableName][] = $Row;
    }
    
    public function PersistAll(array $Rows) {
        array_walk($Rows, [$this, 'Persist']);
    }
    
    private function TriggerEvents(Map $EventMap, $Key) {
        if(isset($EventMap[$Key])) {
            $Events = $EventMap[$Key];
            foreach($Events as $Event) {
                $Event($Key);
            }
        }
    }
    private function AddEvent(Map $EventMap, $Key, callable $Event) {
        if(isset($EventMap[$Key])) {
            $Events = $EventMap[$Key];
            $Events[] = $Event;
        }
        else {
            $EventMap[$Key] = new \ArrayObject([$Event]);
        }
    }
    
    public function TriggerPrePersistEvent(array $Rows) {
        foreach($Rows as $Row) {
            $this->TriggerEvents($this->PrePersistRowEventMap, $Row);
        }
    }    
    public function SubscribeToPrePersistEvent(Row $Row, callable $Event) {
        $this->AddEvent($this->PrePersistRowEventMap, $Row, $Event);
    }
    
    public function TriggerPostPersistEvent(array $Rows) {
        foreach($Rows as $Row) {
            $this->TriggerEvents($this->PostPersistRowEventMap, $Row);
        }
    }    
    public function SubscribeToPostPersistEvent(Row $Row, callable $Event) {
        $this->AddEvent($this->PostPersistRowEventMap, $Row, $Event);
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
    
    public function DiscardAll(array $PrimaryKeys) {
        array_walk($PrimaryKeys, [$this, 'Discard']);
    }
    
    public function DiscardWhere(Criterion $Criterion) {
        $this->DiscardedCriteria[] = $Criterion;
    }
}

?>