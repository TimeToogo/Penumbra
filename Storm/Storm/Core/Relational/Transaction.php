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
     * @var array[] 
     */
    private $PersistedRows = array();
    
    /**
     * @var array[] []
     */
    private $PersistedRowGroups = array();
    
    /**
     * @var Map
     */
    private $PrePersistRowEventMap;
    
    /**
     * @var Map
     */
    private $PostPersistRowEventMap;
    
    /**
     * @var Procedure[]
     */
    private $Procedures = array();
    
    /**
     * @var array[]
     */
    private $DiscardedPrimaryKeys = array();
    
    /**
     * @var array[][]
     */
    private $DiscardedPrimaryKeyGroups = array();
    
    /**
     * @var Criterion[]
     */
    private $DiscardedCriteria = array();
    
    public function __construct() {
        $this->PrePersistRowEventMap = new Map();
        $this->PostPersistRowEventMap = new Map();
    }
    
    /**
     * @return array[]
     */
    public function &GetPersistedRows() {
        return $this->PersistedRows;
    }
    
    /**
     * @return array[][]
     */
    public function &GetPersistedRowGroups() {
        return $this->PersistedRowGroups;
    }
    
    /**
     * @return Procedure[]
     */
    public function GetProcedures() {
        return $this->Procedures;
    }
    
    /**
     * @return array[]
     */
    public function &GetDiscardedPrimaryKeys() {
        return $this->DiscardedPrimaryKeys;
    }
    
    /**
     * @return array[][]
     */
    public function &GetDiscardedPrimaryKeyGroups() {
        return $this->DiscardedPrimaryKeyGroups;
    }
    
    /**
     * @return Criterion[]
     */
    public function GetDiscardedCriteria() {
        return $this->DiscardedCriteria;
    }
    
    /**
     * Persist a row within the transaction.
     * 
     * @param array $Row The row to persist
     * @return void
     */
    public function Persist(Table $Table, array &$Row) {
        $this->PersistedRows[] =& $Row;
        
        $TableName = $Table->GetName();
        if(!isset($this->PersistedRowGroups[$TableName])) {
            $this->PersistedRowGroups[$TableName] = array();
        }
        $this->PersistedRowGroups[$TableName][] =& $Row;
    }
    
    /**
     * Persist an array of rows within the transaction.
     * 
     * @param array[] $Rows The rows to persist
     * @return void
     */
    public function PersistAll(Table $Table, array &$Rows) {
        foreach($Rows as &$Row) {
            $this->Persist($Table, $Row);
        }
    }
    
    /**
     * Trigger all events in an event map with a specific key.
     * 
     * @param Map $EventMap The event map
     * @param object $Key The key
     * @return void
     */
    private function TriggerEvents(Map $EventMap, $Key, array $CustomArguments = array()) {
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
     * @param Table $Table The table to trigger
     * @return void
     */
    public function TriggerPrePersistEvent(Table $Table) {
        $this->TriggerEvents($this->PrePersistRowEventMap, $Table);
    }
    
    /**
     * Subscribe a callback to when the supplied row will be persisted.
     * 
     * @param Table $Table
     * @param callable $Event
     */
    public function SubscribeToPrePersistEvent(Table $Table, callable $Event) {
        $this->AddEvent($this->PrePersistRowEventMap, $Table, $Event);
    }
    
    /**
     * Trigger the post persist callbacks for the supplied rows
     * 
     * @param Table $Table The table to trigger
     * @return void
     */
    public function TriggerPostPersistEvent(Table $Table) {
        $this->TriggerEvents($this->PostPersistRowEventMap, $Table);
    }
    
    /**
     * Subscribe a callback to after the supplied row was persisted.
     * 
     * @param Table $Table
     * @param callable $Event
     * @return void
     */
    public function SubscribeToPostPersistEvent(Table $Table, callable $Event) {
        $this->AddEvent($this->PostPersistRowEventMap, $Table, $Event);
    }
    
    /**
     * Add a procedure to be executed within the transaction.
     * 
     * @param Procedure $Procedure The procedure to execute
     * @return void
     */
    public function Execute(Procedure $Procedure) {
        $this->Procedures[] = $Procedure;
    }
    
    /**
     * Discard a row from its primary key within the transaction.
     * 
     * @param array $PrimaryKey The primary key to discard
     * @return void
     */
    public function Discard(Table $Table, array &$PrimaryKey) {
        $this->DiscardedPrimaryKeys[] =& $PrimaryKey;
        
        $TableName = $Table->GetName();
        if(!isset($this->DiscardedPrimaryKeyGroups[$TableName])) {
            $this->DiscardedPrimaryKeyGroups[$TableName] = array();
        }
        $this->DiscardedPrimaryKeyGroups[$TableName][] =& $PrimaryKey;
    }
    
    /**
     * Discard an array of rows from their primary keys within the transaction.
     * 
     * @param array[] $PrimaryKeys The primary keys to discard
     * @return void
     */
    public function DiscardAll(Table $Table, array &$PrimaryKeys) {
        foreach($PrimaryKeys as &$PrimaryKey) {
            $this->Discard($Table, $PrimaryKey);
        }
    }
    
    /**
     * Discard rows if they match the supplied criterion.
     * 
     * @param Criterion $Criterion The criterion to match
     * @return void
     */
    public function DiscardWhere(Criterion $Criterion) {
        $this->DiscardedCriteria[] = $Criterion;
    }
}

?>