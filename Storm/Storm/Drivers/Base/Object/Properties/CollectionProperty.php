<?php

namespace Storm\Drivers\Base\Object\Properties;

use \Storm\Core\Object;

class CollectionProperty extends MultipleEntityProperty {
    protected function ReviveProxies(Object\Domain $Domain, $Entity, array $Proxies) {
        return new Collections\Collection($this->GetEntityType(), $Proxies);
    }
    
    protected function ReviveCallableProperty(Object\Domain $Domain, $Entity, callable $Callback) {
        return new Collections\LazyCollection($Domain, $this->GetEntityType(), $Callback);
    }
    
    protected function ReviveArrayOfRevivalData(Object\Domain $Domain, $Entity, array $RevivalDataArray) {
        $EntityType = $this->GetEntityType();
        return new Collections\Collection($EntityType, $Domain->ReviveEntities($EntityType, $RevivalDataArray));
    }
    
    protected function PersistRelationshipChanges(Object\Domain $Domain, Object\UnitOfWork $UnitOfWork,
            $ParentEntity, $CurrentValue, $HasOriginalValue, $OriginalValue) {
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
    protected function DiscardRelationshipChanges(Object\Domain $Domain, Object\UnitOfWork $UnitOfWork, 
            $ParentEntity, $CurrentValue, $HasOriginalValue, $OriginalValue) {
        
        $DiscardedRelationships = array();
        if($HasOriginalValue) {
            foreach($OriginalValue->ToArray() as $RemovedEntity) {
                $DiscardedRelationships[] = new Object\RelationshipChange(
                        null, 
                        $this->RelationshipType->GetDiscardedRelationship(
                                $Domain, $UnitOfWork, 
                                $ParentEntity, $RemovedEntity));
            }
        }
        
        return $DiscardedRelationships;
    }
    
    public function ObjectComparison(&$Object1, &$Object2) {
        return $Object1 == $Object2;
    }
}

?>
