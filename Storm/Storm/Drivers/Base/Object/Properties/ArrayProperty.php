<?php

namespace Storm\Drivers\Base\Object\Properties;

use \Storm\Core\Object;
use \Storm\Drivers\Base\Object\LazyRevivalData;
use \Storm\Drivers\Base\Object\MultipleLazyRevivalData;

class ArrayProperty extends MultipleEntityProperty {
    protected function ReviveProxies(array $Proxies) {
        return $Proxies;
    }
    
    protected function ReviveArrayOfRevivalData(array $RevivalDataArray) {
        return $this->RelatedEntityMap->ReviveEntities($RevivalDataArray);
    }
    
    protected function PersistRelationshipChanges(Object\Domain $Domain, Object\UnitOfWork $UnitOfWork,
            $ParentEntity, $CurrentValue, $HasOriginalValue, $OriginalValue) {
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
                    \Storm\Core\Utilities::GetTypeOrClass($CurrentValue));
        }
        
        if($CurrentEntities === $OriginalEntities) {
            return $RelationshipChanges;
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
        
        $DiscarededRelationships = [];
        if($HasOriginalValue) {
            foreach($OriginalValue as $RemovedEntity) {
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
