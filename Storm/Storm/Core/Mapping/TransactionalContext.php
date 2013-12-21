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
    
    final public function PersistStateRelations(Object\State $State) {
        $EntityHash = $State->GetIdentity()->Hash();
        if(isset($this->PersistedEntities[$EntityHash]))
            return;
        
        $this->PersistedEntities[$EntityHash] = true;
        return $this->GetDomainDatabaseMap()->PersistStateRelations($this, $State);
    }
    
    final public function PersistRelations($Entity) {
        $DomainDatabaseMap = $this->GetDomainDatabaseMap();
        $DomainDatabaseMap->EnsureIdentifiable([$Entity]);
        return $this->PersistStateRelations($DomainDatabaseMap->GetDomain()->State($Entity));
    }
    
    final public function PersistAllRelations(array $Entities) {
        $DomainDatabaseMap = $this->GetDomainDatabaseMap();
        $DomainDatabaseMap->EnsureIdentifiable($Entities);
        $Domain = $DomainDatabaseMap->GetDomain();
        $Rows = array();
        foreach($Entities as $Key => $Entity) {
            $Row = $this->PersistStateRelations($Domain->State($Entity));
            if($Row !== null)
                $Rows[$Key] = $Row;
        }
        
        return $Rows;
    }
    
    final public function DiscardIdentityRelations(Object\Identity $Identity) {
        $EntityHash = $Identity->Hash();
        if(isset($this->DiscardedEntities[$EntityHash]))
            return;
        
        $this->DiscardedEntities[$EntityHash] = true;
        return $this->GetDomainDatabaseMap()->DiscardIdentityRelations($this, $Identity);
    }
    
    final public function DiscardRelations($Entity) {
        $DomainDatabaseMap = $this->GetDomainDatabaseMap();
        if(!$DomainDatabaseMap->IsIdenitifiable($Entity))
            return;
        else
            return $this->DiscardIdentityRelations($DomainDatabaseMap->GetDomain()->Identity($Entity));
    }
    
    final public function DiscardAllRelations(array $Entities) {
        $DomainDatabaseMap = $this->GetDomainDatabaseMap();
        $Domain = $DomainDatabaseMap->GetDomain();
        $PrimaryKeys = array();
        foreach($Entities as $Key => $Entity) {
            if($DomainDatabaseMap->IsIdenitifiable($Entity)) {
                $PrimaryKey = $this->DiscardIdentityRelations($Domain->Identity($Entity));
                if($PrimaryKey !== null)
                    $PrimaryKeys[$Key] = $PrimaryKey;
            }
        }
        
        return $PrimaryKeys;
    }
}

?>