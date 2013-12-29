<?php

namespace Storm\Core\Object;

use \Storm\Core\Containers\Registrar;
use \Storm\Core\Containers\Map;

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
        $EntityMap->InititalizeProperties($this);
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
        $EntityTypes = array_reverse(array_merge([get_class($Entity)], class_parents($Entity, false)));
        foreach($EntityTypes as $EntityType) {
            if(isset($this->EntityMaps[$EntityType])) {
                return $this->EntityMaps[$EntityType];
            }
        }           
        throw new \Storm\Core\Exceptions\UnmappedEntityException($EntityType);
    }
    
    /**
     * @param object $Entity
     * @return Identity
     */
    final public function HasIdentity($Entity) {
        return $this->VerifyEntity($Entity)->HasIdentity($Entity);
    }
    
    final public function ShareIdentity($Entity, $OtherEntity) {
        $EntityMap = $this->VerifyEntity($Entity);
        $EntityType = $EntityMap->GetEntityType();
        if(!($OtherEntity instanceof $EntityType)) {
            return false;
        }
        else {
            return $EntityMap->Identity($Entity)->Matches($EntityMap->Identity($OtherEntity));
        }
    }
    
    /**
     * @param object $Entity
     * @return Identity
     */
    final public function Identity($Entity) {
        return $this->VerifyEntity($Entity)->Identity($Entity);
    }
    
    final public function Apply($Entity, PropertyData $PropertyData) {
        return $this->VerifyEntity($Entity)->Apply($this, $Entity, $PropertyData);
    }
    
    
    /**
     * @param object $Entity
     * @return Relationship
     */
    final public function Relationship($ParentEntity, $ChildEntity) {
        $ParentIdentity = $this->VerifyEntity($ParentEntity)->Identity($ParentEntity);
        $ChildIdentity = $this->VerifyEntity($ChildEntity)->Identity($ChildEntity);
        
        return new Relationship($ParentIdentity, $ChildIdentity);
    }
    
    /**
     * @return PersistenceData
     */
    final public function Persist(UnitOfWork $UnitOfWork, $Entity) {
        return $this->VerifyEntity($Entity)->Persist($UnitOfWork, $Entity);
    }
    
    /**
     * @return PersistenceData
     */
    final public function Discard(UnitOfWork $UnitOfWork, $Entity) {
        return $this->VerifyEntity($Entity)->Discard($UnitOfWork, $Entity);
    }
    
    final public function ReviveEntities($EntityType, array $RevivalData) {
        if(count($RevivalData) === 0) {
            return array();
        }
        $EntityMap = $this->GetEntityMap($EntityType);
        
        return $EntityMap->ReviveEntities($this, $RevivalData);
    }
    
    final public function ReviveEntityInstances($EntityType, Map $StateInstanceMap) {
        $EntityMap = $this->GetEntityMap($EntityType);
        
        return $EntityMap->ReviveEntityInstances($this, $StateInstanceMap);
    }
    
    final public function DiscardWhere(UnitOfWork $UnitOfWork, IRequest $Request) {
        $this->GetEntityMap($Request->GetEntityType())->DiscardWhere($UnitOfWork, $Request);
    }
    
    /**
     * @return UnitOfWork
     */
    final public function BuildUnitOfWork(
            array $EntitiesToPersist = array(),
            array $OperationsToExecute = array(),
            array $EntitiesToDiscard = array(), 
            array $RequestsToDiscard = array()) {
        $UnitOfWork = new UnitOfWork($this);
        
        foreach($EntitiesToPersist as $Entity) {
            $UnitOfWork->Persist($Entity);
        }
        foreach($OperationsToExecute as $Operation) {
            $UnitOfWork->Execute($Operation);
        }
        foreach($EntitiesToDiscard as $Entity) {
            $UnitOfWork->Discard($Entity);
        }
        foreach($RequestsToDiscard as $Request) {
            $UnitOfWork->DiscardWhere($Request);
        }
        
        return $UnitOfWork;
    }
    
}

?>