<?php

namespace Storm\Core;

use \Storm\Core\Object;
use \Storm\Core\Mapping\DomainDatabaseMap;

class Repository {
    private $ORM;
    private $EntityType;
    private $AutoSave;
    private $PersistedQueue = array();
    private $DiscardedQueue = array();
    private $DiscardedRequestQueue = array();
    
    final public function __construct(DomainDatabaseMap $ORM, $EntityType, $AutoSave) {
        $this->ORM = $ORM;
        $this->EntityType = $EntityType;
        $this->AutoSave = $AutoSave;
    }
    
    public function Load(Object\IRequest $Request) {
        return $this->ORM->Load($Request);
    }
    
    private function VerifyEntity($Entity) {
        if(!($Entity instanceof $this->EntityType))
            throw new \InvalidArgumentException('$Entity must be a valid instance of ' . $this->EntityType);
    }
    
    public function Persist($Entity) {
        $this->VerifyEntity($Entity);
        $this->PersistedQueue[] = $Entity;
        $this->AutoSave();
    }
    public function PersistAll(array $Entities) {
        $this->PersistedQueue = array_merge($this->PersistedQueue, $Entities);
        $this->AutoSave();
    }
    
    public function Discard(&$Entity) {
        $this->VerifyEntity($Entity);
        $this->DiscardedQueue[] = $Entity;
        $this->AutoSave();
    }
    public function DiscardAll(array $Entities) {
        $this->DiscardedQueue = array_merge($this->DiscardedQueue, $Entities);
        $this->AutoSave();
    }
    
    public function DiscardWhere(Object\IRequest $Request) {
        $this->DiscardedRequestQueue[] = $Request;
        $this->AutoSave();
    }
    
    private function AutoSave() {
        if($this->AutoSave)
            $this->SaveChanges();
    }
    
    public function SaveChanges() {
        if(count($this->PersistedQueue) === 0 && 
                count($this->DiscardedQueue) === 0 && count($this->DiscardedRequestQueue) === 0)
            return;
        
        $this->ORM->Commit($this->PersistedQueue, $this->DiscardedQueue, $this->DiscardedRequestQueue);
        $this->PersistedQueue = array();
        $this->DiscardedQueue = array();
        $this->DiscardedRequestQueue = array();
    }
}

?>