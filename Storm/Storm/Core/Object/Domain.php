<?php

namespace Storm\Core\Object;

use \Storm\Core\Containers\Registrar;
use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping\DomainDatabaseMap;

abstract class Domain {
    use \Storm\Core\Helpers\Type;
    
    private $EntityMaps = array();
    
    public function __construct() {
        $Registrar = new Registrar(EntityMap::GetType());
        $this->RegisterEntityMaps($Registrar);
        foreach($Registrar->GetRegistered() as $EntityMap) {
            $this->AddEntityMap($EntityMap);
        }
    }
    protected abstract function RegisterEntityMaps(Registrar $Registrar);
    
    final protected function AddEntityMap(EntityMap $EntityMap) {
        $this->EntityMaps[$EntityMap->GetEntityType()] = $EntityMap;
    }
    
    /**
     * @param string $EntityType
     * @return bool
     */
    final public function HasEntityMap($EntityType) {
        return isset($this->EntityMaps[$EntityType]);
    }
    
    /**
     * @param string $EntityType
     * @return EntityMap
     */
    final public function GetEntityMap($EntityType) {
        return $this->HasEntityMap($EntityType) ? $this->EntityMaps[$EntityType] : null;
    }
    
    /**
     * @return EntityMap
     */
    private function VerifyEntity($Entity) {
        $EntityType = get_class($Entity);
        if(!$this->HasEntityMap($EntityType))
            throw new \Storm\Core\Exceptions\UnmappedEntityException($EntityType);
        else
            return $this->GetEntityMap($EntityType);
    }
    
    /**
     * @param object $Entity
     * @return Identity
     */
    final public function HasIdentity($Entity) {
        return $this->VerifyEntity($Entity)->HasIdentity($Entity);
    }
    
    /**
     * @param object $Entity
     * @return Identity
     */
    final public function Identity($Entity) {
        return $this->VerifyEntity($Entity)->Identity($Entity);
    }
    
    /**
     * @param object $Entity
     * @return Identity
     */
    final public function SetIdentity($Entity, Identity $Identity) {
        return $this->VerifyEntity($Entity)->SetIdentity($Identity);
    }
    
    /**
     * @param object $Entity
     * @return State
     */
    final public function State($Entity) {
        return $this->VerifyEntity($Entity)->State($Entity);
    }
    
    final public function ReviveEntities($EntityType, array $States) {
        $EntityMap = $this->GetEntityMap($EntityType);
        
        return $EntityMap->ReviveEntities($States);
    }
    
    final public function ReviveEntityInstances($EntityType, Map $StateInstanceMap) {
        $EntityMap = $this->GetEntityMap($EntityType);
        
        return $EntityMap->ReviveEntityInstances($StateInstanceMap);
    }
    
    final public function Persist(UnitOfWork $UnitOfWork, $Entity) {
        $this->VerifyEntity($Entity)->Persist($UnitOfWork, $Entity);
    }
    
    final public function Discard(UnitOfWork $UnitOfWork, $Entity) {
        $this->VerifyEntity($Entity)->Discard($UnitOfWork, $Entity);
    }
    
    final public function DiscardWhere(UnitOfWork $UnitOfWork, IRequest $Request) {
        $this->GetEntityMap($Request->GetEntityType())->DiscardWhere($UnitOfWork, $Request);
    }
    
    
    /**
     * @return UnitOfWork
     */
    final public function BuildUnitOfWork(array $PersistanceEntities = array(), 
            array $DiscardationEntities = array(), array $DiscardedRequests = array()) {
        $UnitOfWork = new UnitOfWork();
        
        foreach($PersistanceEntities as $Entity) {
            $this->Persist($UnitOfWork, $Entity);
        }
        foreach($DiscardationEntities as $Entity) {
            $this->Discard($UnitOfWork, $Entity);
        }
        foreach($DiscardedRequests as $Request) {
            $this->DiscardWhere($UnitOfWork, $Request);
        }
        
        return $UnitOfWork;
    }
    
}

?>