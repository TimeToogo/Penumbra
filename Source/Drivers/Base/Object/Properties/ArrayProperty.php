<?php

namespace Penumbra\Drivers\Base\Object\Properties;

use \Penumbra\Core\Object;
use \Penumbra\Drivers\Base\Object\LazyRevivalData;
use \Penumbra\Drivers\Base\Object\MultipleLazyRevivalData;

class ArrayProperty extends MultipleEntityProperty {
    
    protected function UpdateAccessor(Accessors\Accessor $Accessor) {
        return new self(
                $Accessor, 
                $this->RelatedEntityType,
                $this->RelationshipType,
                $this->BackReferenceProperty,
                $this->ProxyGenerator);
    }
    
    protected function ReviveProxies(array $Proxies) {
        return $Proxies;
    }
    
    protected function ReviveArrayOfRevivalData(array $RevivalDataArray) {
        return $this->RelatedEntityMap->ReviveEntities($RevivalDataArray);
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
        
        if($HasOriginalValue) {
            $OriginalEntities =& $OriginalValue;
        }
        
        if(is_array($CurrentValue)) {
            $CurrentEntities =& $CurrentValue;
        }
        else {
            throw new Object\ObjectException(
                    'Invalid value for property on entity %s, array expected, %s given',
                    $this->GetEntityType(),
                    \Penumbra\Utilities\Type::GetTypeOrClass($CurrentValue));
        }
        
        if($CurrentEntities === $OriginalEntities) {
            return $RelationshipChanges;
        }
        
        $NewEntities = array_udiff($CurrentEntities, $OriginalEntities, [$this, 'ObjectComparison']);
        $RemovedEntities = array_udiff($OriginalEntities, $CurrentEntities, [$this, 'ObjectComparison']);
        
        foreach($NewEntities as $NewEntity) {
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
        
        $DiscarededRelationships = [];
        if($HasOriginalValue) {
            foreach($OriginalValue as $RemovedEntity) {
                $DiscarededRelationships[] = new Object\RelationshipChange(
                        null, 
                        $this->RelationshipType->GetDiscardedIdentity($Domain, $UnitOfWork, $RemovedEntity));
            }
        }
        
        return $DiscarededRelationships;
    }
    
    public function ObjectComparison(&$Object1, &$Object2) {
        return $Object1 == $Object2;
    }
}

?>