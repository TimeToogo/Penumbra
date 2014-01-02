<?php

namespace Storm\Drivers\Base\Object\Properties\Types;

use \Storm\Core\Object;
use \Storm\Core\Object\Domain;
use \Storm\Core\Object\UnitOfWork;

class RelatedEntityCollectionType extends RelatedType {
    private $BackReferenceProperty;
    private $CascadePersist;
    private $CascadeDiscard;
    public function __construct(
            $EntityType,
            IProperty $BackReferenceProperty = null, 
            $CascadePersist = true, 
            $CascadeDiscard = false) {
        parent::__construct($EntityType);
        $this->BackReferenceProperty = $BackReferenceProperty;
        $this->CascadePersist = $CascadePersist;
        $this->CascadeDiscard = $CascadeDiscard;
    }
    
    public function ReviveValue(Domain $Domain, $Entity, $RevivalValue) {
        if(is_array($RevivalValue)) {
            if(count(array_filter($RevivalValue, function ($Value) { return $Value instanceof Object\RevivalData; })) === count($RevivalValue)) {
                if($this->BackReferenceProperty !== null) {
                    foreach($RevivalValue as $RevivalData) {
                        $RevivalData[$this->BackReferenceProperty] = $Entity;
                    }
                }
                return new Collections\Collection($Domain, $this->EntityType, $RevivalValue);
            }
        }
        else if(is_callable($RevivalValue)) {
            $LoadFunction = $RevivalValue;
            if($this->BackReferenceProperty !== null) {
                $LoadFunction = function () use(&$RevivalValue, &$Entity) {
                    $RevivalDataArray = $RevivalValue();
                    foreach($RevivalDataArray as $RevivalData) {
                        $RevivalData[$this->BackReferenceProperty] = $Entity;
                    }

                    return $RevivalDataArray;
                };
            }

            return new Collections\LazyCollection($Domain, $this->EntityType, $LoadFunction);
        }
        else {
            throw new \Exception;//TODO: error message
        }
    }
    
    public function Persist(UnitOfWork $UnitOfWork, $Entity, $PropertyValue) {
        if($this->CascadePersist) {
            if(!($PropertyValue instanceof Collections\ICollection)) {
                throw new \Exception;//TODO:error message
            }
            
            $PersistedData = array();
            $DiscardedIdentities = array();
            foreach($PropertyValue as $Entity) {
                $PersistedData[] = $UnitOfWork->Persist($Entity);
            }
            foreach($PropertyValue->__GetRemovedEntities() as $Entity) {
                $DiscardedIdentities[] = $UnitOfWork->Discard($Entity);
            }
            
            return new CollectionPersistanceData($PersistedData, $DiscardedIdentities);
        }
    }
    public function Discard(UnitOfWork $UnitOfWork, $Entity, $PropertyValue) {
        if($this->CascadeDiscard) {
            if(!($PropertyValue instanceof Collections\ICollection)) {
                throw new \Exception;//TODO:error message
            }
            
            $DiscardedIdentities = array();
            foreach($PropertyValue->__GetOriginalEntities() as $Entity) {
                $DiscardedIdentities[] = $UnitOfWork->Discard($Entity);
            }
            
            return $DiscardedIdentities;
        }
    }
}

?>
