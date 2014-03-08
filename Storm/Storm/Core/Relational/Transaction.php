<?php

namespace Storm\Core\Relational;

use Storm\Core\Containers\Map;

/**
 * This transaction represents operations to commit against a database.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class Transaction {
    /**
     * @var Row[] 
     */
    private $PersistedRows = [];
    
    /**
     * @var Row[] []
     */
    private $PersistedRowGroups = [];
    
    /**
     * @var Map
     */
    private $PrePersistRowEventMap;
    
    /**
     * @var Map
     */
    private $PostPersistRowEventMap;
    
    /**
     * @var Update[]
     */
    private $Updates = [];
    
    /**
     * @var PrimaryKey[]
     */
    private $DiscardedPrimaryKeys = [];
    
    /**
     * @var PrimaryKey[][]
     */
    private $DiscardedPrimaryKeyGroups = [];
    
    /**
     * @var Delete[]
     */
    private $Deletes = [];
    
    public function __construct() {
        $this->PrePersistRowEventMap = new Map();
        $this->PostPersistRowEventMap = new Map();
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
     * @return Update[]
     */
    public function GetUpdates() {
        return $this->Updates;
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
     * @return Delete[]
     */
    public function GetDeletes() {
        return $this->Deletes;
    }
    
    /**
     * Persist a row within the transaction.
     * 
     * @param Row $Row The row to persist
     * @return void
     */
    public function Persist(Row $Row) {
        $Hash = spl_object_hash($Row);
        if(isset($this->PersistedRows[$Hash])) {
            return;
        }
        $this->PersistedRows[$Hash] = $Row;
        
        $TableName = $Row->GetTable()->GetName();
        if(!isset($this->PersistedRowGroups[$TableName])) {
            $this->PersistedRowGroups[$TableName] = [];
        }
        $this->PersistedRowGroups[$TableName][] = $Row;
    }
    
    /**
     * Persist an array of rows within the transaction.
     * 
     * @param Row[] $Rows The rows to persist
     * @return void
     */
    public function PersistAll(array $Rows) {
        array_walk($Rows, [$this, 'Persist']);
    }
    
    /**
     * Trigger all events in an event map with a specific key.
     * 
     * @param Map $EventMap The event map
     * @param object $Key The key
     * @return void
     */
    private function TriggerEvents(Map $EventMap, $Key, array $CustomArguments = []) {
        array_unshift($CustomArguments, $this);
        if(isset($EventMap[$Key])) {
            $Events = $EventMap[$Key];
            foreach($Events as $Event) {
                call_user_func_array($Event, $CustomArguments);
            }
        }
    }
    
    /**
     * Add an event to an event map
     * 
     * @param Map $EventMap
     * @param objecy $Key The key of the event map
     * @param callable $Event
     * @return void
     */
    private function AddEvent(Map $EventMap, $Key, callable $Event) {
        if(isset($EventMap[$Key])) {
            $Events = $EventMap[$Key];
            $Events[] = $Event;
        }
        else {
            $EventMap[$Key] = new \ArrayObject([$Event]);
        }
    }
    
    /**
     * Trigger the pre persist callbacks for the supplied rows
     * 
     * @param ITable $Table The table to trigger
     * @return void
     */
    public function TriggerPrePersistEvent(ITable $Table) {
        $this->TriggerEvents($this->PrePersistRowEventMap, $Table);
    }
    
    /**
     * Subscribe a callback to when the supplied row will be persisted.
     * 
     * @param ITable $Table
     * @param callable $Event
     */
    public function SubscribeToPrePersistEvent(ITable $Table, callable $Event) {
        $this->AddEvent($this->PrePersistRowEventMap, $Table, $Event);
    }
    
    /**
     * Trigger the post persist callbacks for the supplied rows
     * 
     * @param ITable $Table The table to trigger
     * @return void
     */
    public function TriggerPostPersistEvent(ITable $Table) {
        $this->TriggerEvents($this->PostPersistRowEventMap, $Table);
    }
    
    /**
     * Subscribe a callback to after the supplied row was persisted.
     * 
     * @param ITable $Table
     * @param callable $Event
     * @return void
     */
    public function SubscribeToPostPersistEvent(ITable $Table, callable $Event) {
        $this->AddEvent($this->PostPersistRowEventMap, $Table, $Event);
    }
    
    /**
     * Add a procedure to be executed within the transaction.
     * 
     * @param Update $Update The procedure to execute
     * @return void
     */
    public function AddUpdate(Update $Update) {
        $this->Updates[] = $Update;
    }
    
    /**
     * Discard a row from its primary key within the transaction.
     * 
     * @param PrimaryKey $PrimaryKey The primary key to discard
     * @return void
     */
    public function Discard(PrimaryKey $PrimaryKey) {
        $this->DiscardedPrimaryKeys[] = $PrimaryKey;
        
        $TableName = $PrimaryKey->GetTable()->GetName();
        if(!isset($this->DiscardedPrimaryKeyGroups[$TableName])) {
            $this->DiscardedPrimaryKeyGroups[$TableName] = [];
        }
        $this->DiscardedPrimaryKeyGroups[$TableName][] = $PrimaryKey;
    }
    
    /**
     * Discard an array of rows from their primary keys within the transaction.
     * 
     * @param PrimaryKey[] $PrimaryKeys The primary keys to discard
     * @return void
     */
    public function DiscardAll(array $PrimaryKeys) {
        array_walk($PrimaryKeys, [$this, 'Discard']);
    }
    
    /**
     * Added the supplied delete query
     * 
     * @param Delete $Delete The delete query to execute
     * @return void
     */
    public function AddDelete(Delete $Delete) {
        $this->Deletes[] = $Delete;
    }
}

?>