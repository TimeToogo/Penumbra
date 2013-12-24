<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Relational;
use \Storm\Core\Object;

final class TransactionalContext extends MappingContext {
    private $Transaction;
    
    private $PersistedEntities = array();
    private $DiscardedEntities = array();
    
    public function __construct(DomainDatabaseMap $DomainDatabaseMap, Relational\Transaction $Transaction) {
        parent::__construct($DomainDatabaseMap);
        
        $this->Transaction = $Transaction;
    }
    
    /**
     * @return Relational\Transaction
     */
    final public function GetTransaction() {
        return $this->Transaction;
    }
    
    final public function PersistState(Object\State $State) {
        $EntityHash = $State->GetIdentity()->Hash();
        if(isset($this->PersistedEntities[$EntityHash]))
            return;
        
        $this->PersistedEntities[$EntityHash] = true;
        return $this->GetDomainDatabaseMap()->PersistState($this, $State);
    }
    
    final public function Persist($Entity) {
        $DomainDatabaseMap = $this->GetDomainDatabaseMap();
        $DomainDatabaseMap->EnsureIdentifiable([$Entity]);
        return $this->PersistState($DomainDatabaseMap->GetDomain()->State($Entity));
    }
    
    final public function PersistAll(array $Entities) {
        $DomainDatabaseMap = $this->GetDomainDatabaseMap();
        $DomainDatabaseMap->EnsureIdentifiable($Entities);
        $Domain = $DomainDatabaseMap->GetDomain();
        $Rows = array();
        foreach($Entities as $Key => $Entity) {
            $Row = $this->PersistState($Domain->State($Entity));
            if($Row !== null)
                $Rows[$Key] = $Row;
        }
        
        return $Rows;
    }
    
    final public function DiscardIdentity(Object\Identity $Identity) {
        $EntityHash = $Identity->Hash();
        if(isset($this->DiscardedEntities[$EntityHash]))
            return;
        
        $this->DiscardedEntities[$EntityHash] = true;
        return $this->GetDomainDatabaseMap()->DiscardIdentity($this, $Identity);
    }
    
    final public function Discard($Entity) {
        $DomainDatabaseMap = $this->GetDomainDatabaseMap();
        if(!$DomainDatabaseMap->IsIdenitifiable($Entity))
            return;
        else
            return $this->DiscardIdentity($DomainDatabaseMap->GetDomain()->Identity($Entity));
    }
    
    final public function DiscardAll(array $Entities) {
        $DomainDatabaseMap = $this->GetDomainDatabaseMap();
        $Domain = $DomainDatabaseMap->GetDomain();
        $PrimaryKeys = array();
        foreach($Entities as $Key => $Entity) {
            if($DomainDatabaseMap->IsIdenitifiable($Entity)) {
                $PrimaryKey = $this->DiscardIdentity($Domain->Identity($Entity));
                if($PrimaryKey !== null)
                    $PrimaryKeys[$Key] = $PrimaryKey;
            }
        }
        
        return $PrimaryKeys;
    }
}

?>