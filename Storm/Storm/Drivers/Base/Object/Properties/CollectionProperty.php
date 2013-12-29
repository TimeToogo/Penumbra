<?php

namespace Storm\Drivers\Base\Object\Properties;

use \Storm\Core\Object;

class CollectionProperty extends RelationshipProperty implements Object\IEntityProperty {
    private $ProxyGenerator;
    
    public function __construct(
            Accessors\Accessor $Accessor,
            $EntityType,
            $IsIdentifying = true,
            Proxies\IProxyGenerator $ProxyGenerator = null) {
        parent::__construct($Accessor, $EntityType, $IsIdentifying);
        
        $this->ProxyGenerator = $ProxyGenerator;
    }
    
    protected function ReviveArrayOfCallables(Object\Domain $Domain, $Entity, array $Callbacks) {
        if($this->ProxyGenerator !== null) {
            $EntityType = $this->GetEntityType();
            $Proxies = $this->ProxyGenerator->GenerateProxies($Domain, $EntityType, $Callbacks);
            return new Collections\Collection($EntityType, $Proxies);
        }
        else {
            throw new Exception;//TODO:error
        }
    }
    
    protected function ReviveCallable(Object\Domain $Domain, $Entity, $Callback) {
        return new Collections\LazyCollection($Domain, $this->GetEntityType(), $Callback);
    }
    
    protected function ReviveArrayOfRevivalData(Object\Domain $Domain, $Entity, array $RevivalDataArray) {
        $EntityType = $this->GetEntityType();
        return new Collections\Collection($EntityType, $Domain->ReviveEntities($EntityType, $RevivalDataArray));
    }
    
    public function Persist(Object\UnitOfWork $UnitOfWork, $ParentEntity) {
        $Domain = $UnitOfWork->GetDomain();
        list(
                $CurrentValue, 
                $HasOriginalValue, 
                $OriginalValue) = $this->GetEntityRelationshipData($ParentEntity);
        
        $PersistedRelationships = array();
        $DiscarededRelationships = array();
        
        $OriginalEntities = array();
        $CurrentEntities = array();
        
        if($HasOriginalValue) {
            $OriginalEntities = $OriginalValue->ToArray();
        }
        
        if(!($CurrentValue instanceof Collections\ICollection)) {
            if(!($CurrentValue instanceof \Traversable)) {
                throw new Exception;//TODO:error message
            }
            foreach($CurrentValue as $Entity) {
                if($this->IsValidEntity($Entity)) {
                    $CurrentEntities[] = $Entity;
                }
            }
        }
        else if($CurrentValue == $OriginalValue && !$CurrentValue->__IsAltered()) {
            
        }
        else {
            $CurrentEntities = $CurrentValue->ToArray();
        }
        $NewEntities = array_udiff($CurrentEntities, $OriginalEntities, [$this, 'ObjectComparison']);
        $RemovedEntities = array_udiff($OriginalEntities, $CurrentEntities, [$this, 'ObjectComparison']);

        foreach($NewEntities as $NewEntity) {
            $UnitOfWork->Persist($NewEntity);
            $PersistedRelationships[] = new Object\RelationshipChange($Domain->Relationship($ParentEntity, $NewEntity), null);
        }
        foreach($RemovedEntities as $RemovedEntity) {
            if($this->IsIdentifying()) {
                $UnitOfWork->Discard($RemovedEntity);
            }
            $DiscarededRelationships[] = new Object\RelationshipChange(null, $Domain->Relationship($ParentEntity, $RemovedEntity));
        }
        
        return array_merge($PersistedRelationships, $DiscarededRelationships);
    }
    protected function GetOriginalEntities($OriginalValue) {
        
    }
    
    public function Discard(Object\UnitOfWork $UnitOfWork, $ParentEntity) {
        $Domain = $UnitOfWork->GetDomain();
        list(
                $CurrentValue, 
                $HasOriginalValue, 
                $OriginalValue) = $this->GetEntityRelationshipData($ParentEntity);
        
        $DiscarededRelationships = array();
        if($HasOriginalValue) {
            foreach($OriginalValue->ToArray() as $RemovedEntity) {
                if($this->IsIdentifying()) {
                    $UnitOfWork->Discard($RemovedEntity);
                }
                $DiscarededRelationships[] = new Object\RelationshipChange(null, $Domain->Relationship($ParentEntity, $RemovedEntity));
            }
        }
        
        return $DiscarededRelationships;
    }
    
    public function ObjectComparison(&$Object1, &$Object2) {
        return $Object1 == $Object2;
    }
}

?>
