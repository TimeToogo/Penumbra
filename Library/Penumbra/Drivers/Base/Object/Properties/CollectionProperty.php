<?php

namespace Penumbra\Drivers\Base\Object\Properties;

use \Penumbra\Core\Object;
use \Penumbra\Drivers\Base\Object\LazyRevivalData;
use \Penumbra\Drivers\Base\Object\MultipleLazyRevivalData;

class CollectionProperty extends MultipleEntityProperty {
    
    protected function UpdateAccessor(Accessors\Accessor $Accessor) {
        return new self(
                $Accessor, 
                $this->RelatedEntityType,
                $this->RelationshipType,
                $this->BackReferenceProperty,
                $this->ProxyGenerator);
    }
    
    protected function ReviveProxies(array $Proxies) {
        return new Collections\Collection($this->RelatedEntityType, $Proxies);
    }
    
    protected function ReviveMultipleLazyRevivalData(MultipleLazyRevivalData $LazyRevivalData) {
        return new Collections\LazyCollection($this->RelatedEntityMap,
                $LazyRevivalData->GetAlreadyKnownRevivalData(), 
                $LazyRevivalData->GetMultipleRevivalDataLoader(),
                $this->ProxyGenerator);
    }
    
    protected function ReviveArrayOfRevivalData(array $RevivalDataArray) {
        return new Collections\Collection($this->GetEntityType(), $this->RelatedEntityMap->ReviveEntities($RevivalDataArray));
    }
    
    protected function PersistRelationshipChanges(
            Object\Domain $Domain, 
            Object\UnitOfWork $UnitOfWork,
            $CurrentValue, 
            $HasOriginalValue, 
            $OriginalValue) {
        $RelationshipChanges = [];
        
        $OriginalEntities = [];
        $CurrentEntities = [];
        
        if(!($CurrentValue instanceof Collections\ICollection)) {
            if(!($CurrentValue instanceof \Traversable)) {
            throw new Object\ObjectException(
                    'Invalid value for collection property %s on entity %s: \Traversable expected, %s given',
                    $this->GetIdentifier(),
                    $this->GetEntityType(),
                    \Penumbra\Utilities\Type::GetTypeOrClass($CurrentValue));
            }
            foreach($CurrentValue as $Entity) {
                if($this->IsValidEntity($Entity)) {
                    $CurrentEntities[] = $Entity;
                }
            }
        }
        else if($CurrentValue instanceof Collections\LazyCollection && !$CurrentValue->__IsLoaded()) {
            return [];
        }
        else {
            $CurrentEntities = $CurrentValue->ToArray();
        }
        
        if($HasOriginalValue) {
            $OriginalEntities = $OriginalValue->ToArray();
        }
        $NewOrAlteredEntities = $this->ComputeDifference($CurrentEntities, $OriginalEntities);
        $RemovedEntities = $this->ComputeIdentityDifference($OriginalEntities, $CurrentEntities);
        
        foreach($NewOrAlteredEntities as $NewEntity) {
            $RelationshipChanges[] = new Object\RelationshipChange(
                    $this->RelationshipType->GetPersistedEntityData($Domain, $UnitOfWork, $NewEntity), 
                    null);
        }
        foreach($RemovedEntities as $RemovedEntity) {
            $RelationshipChanges[] = new Object\RelationshipChange(
                    null, 
                    $this->RelationshipType->GetDiscardedIdentity($Domain, $UnitOfWork, $RemovedEntity));
        }
        
        return $RelationshipChanges;
    }
    
    protected function DiscardRelationshipChanges(
            Object\Domain $Domain, 
            Object\UnitOfWork $UnitOfWork, 
            $CurrentValue, 
            $HasOriginalValue, 
            $OriginalValue) {
        
        $DiscardedRelationships = [];
        if($HasOriginalValue) {
            foreach($OriginalValue->ToArray() as $RemovedEntity) {
                $DiscardedRelationships[] = new Object\RelationshipChange(
                        null, 
                        $this->RelationshipType->GetDiscardedIdentity($Domain, $UnitOfWork, $RemovedEntity));
            }
        }
        
        return $DiscardedRelationships;
    }
    
    private function ComputeDifference(array $Objects, array $OtherObjects) {
        $Difference = [];
        foreach($Objects as $Object) {
            if(!in_array($Object, $OtherObjects)) {
                $Difference[] = $Object;
            }
        }
        
        return $Difference;
    }
    
    private function ComputeIdentityDifference(array $Objects, array $OtherObjects) {
        $this->IndexEntitiesByIdentity($Objects);
        $this->IndexEntitiesByIdentity($OtherObjects);
        
        return array_diff_key($Objects, $OtherObjects);
    }
    
    private function IndexEntitiesByIdentity(array &$Entities) {
        $IndexedEntities = [];
        foreach($Entities as $Entity) {
            $IndexedEntities[$this->RelatedEntityMap->Identity($Entity)->Hash()] = $Entity;
        }
        
        $Entities = $IndexedEntities;
    }
}

?>