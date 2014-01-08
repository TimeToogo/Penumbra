<?php

namespace Storm\Drivers\Base\Object\Properties;

use \Storm\Core\Object;

class CollectionProperty extends RelationshipProperty implements Object\ICollectionProperty {
    private $RelationshipType;
    private $ProxyGenerator;
    
    public function __construct(
            Accessors\Accessor $Accessor,
            $EntityType,
            IRelationshipType $RelationshipType,
            Object\IProperty $BackReferenceProperty = null,
            Proxies\IProxyGenerator $ProxyGenerator = null) {
        parent::__construct($Accessor, $EntityType, $RelationshipType->IsIdentifying(), $BackReferenceProperty);
        
        $this->RelationshipType = $RelationshipType;
        $this->ProxyGenerator = $ProxyGenerator;
    }
    
    protected function ReviveArrayOfCallables(Object\Domain $Domain, $Entity, array $Callbacks, Object\IProperty $BackReferenceProperty = null) {
        if($this->ProxyGenerator !== null) {
            if($BackReferenceProperty !== null) {
                foreach($Callbacks as $Key => $Callback) {
                    $Callbacks[$Key] = function() use($Callback, &$BackReferenceProperty, $Entity) {
                        $RevivalData = call_user_func_array($Callback, func_get_args());
                        $RevivalData[$BackReferenceProperty] = $Entity;
                        
                        return $RevivalData;
                    };
                }
            }
            $EntityType = $this->GetEntityType();
            $Proxies = $this->ProxyGenerator->GenerateProxies($Domain, $EntityType, $Callbacks);
            return new Collections\Collection($EntityType, $Proxies);
        }
        else {
            throw new \Exception;//TODO:error
        }
    }
    
    protected function ReviveCallable(Object\Domain $Domain, $Entity, callable $Callback, Object\IProperty $BackReferenceProperty = null) {
        if($BackReferenceProperty !== null) {
            $Callback = function () use ($Callback, &$BackReferenceProperty, &$Entity) {
                $RevivalDataArray = call_user_func_array($Callback, func_get_args());
                foreach($RevivalDataArray as $RevivalData) {
                    $RevivalData[$BackReferenceProperty] = $Entity;
                }
                
                return $RevivalDataArray;
            };
        }
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
        
        $RelationshipChanges = array();
        
        $OriginalEntities = array();
        $CurrentEntities = array();
        
        if($HasOriginalValue) {
            $OriginalEntities = $OriginalValue->ToArray();
        }
        
        if(!($CurrentValue instanceof Collections\ICollection)) {
            if(!($CurrentValue instanceof \Traversable)) {
                throw new \Exception;//TODO:error message
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
            $RelationshipChanges[] = new Object\RelationshipChange(
                    $this->RelationshipType->GetPersistedRelationship(
                            $Domain, $UnitOfWork, 
                            $ParentEntity, $NewEntity), 
                    null);
        }
        foreach($RemovedEntities as $RemovedEntity) {
            $RelationshipChanges[] = new Object\RelationshipChange(
                    null, 
                    $this->RelationshipType->GetDiscardedRelationship(
                            $Domain, $UnitOfWork, 
                            $ParentEntity, $RemovedEntity));
        }
        
        return $RelationshipChanges;
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
                $DiscarededRelationships[] = new Object\RelationshipChange(
                        null, 
                        $this->RelationshipType->GetDiscardedRelationship(
                                $Domain, $UnitOfWork, 
                                $ParentEntity, $RemovedEntity));
            }
        }
        
        return $DiscarededRelationships;
    }
    
    public function ObjectComparison(&$Object1, &$Object2) {
        return $Object1 == $Object2;
    }
}

?>
