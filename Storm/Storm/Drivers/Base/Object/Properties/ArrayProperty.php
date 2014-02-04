<?php

namespace Storm\Drivers\Base\Object\Properties;

use \Storm\Core\Object;
use \Storm\Drivers\Base\Object\LazyRevivalData;
use \Storm\Drivers\Base\Object\MultipleLazyRevivalData;

class ArrayProperty extends MultipleEntityProperty {
    protected function ReviveProxies(Object\Domain $Domain, $Entity, array $Proxies) {
        return $Proxies;
    }
    
    protected function ReviveArrayOfRevivalData(Object\Domain $Domain, $Entity, array $RevivalDataArray) {
        return $Domain->ReviveEntities($this->GetEntityType(), $RevivalDataArray);
    }
    
    protected function PersistRelationshipChanges(Object\Domain $Domain, Object\UnitOfWork $UnitOfWork,
            $ParentEntity, $CurrentValue, $HasOriginalValue, $OriginalValue) {
        $RelationshipChanges = array();
        
        $OriginalEntities = array();
        $CurrentEntities = array();
        
        if($HasOriginalValue) {
            $OriginalEntities =& $OriginalValue;
        }
        
        if(is_array($CurrentValue)) {
            $CurrentEntities =& $CurrentValue;
        }
        else {
            throw new \Exception();
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
        
        $DiscarededRelationships = array();
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
