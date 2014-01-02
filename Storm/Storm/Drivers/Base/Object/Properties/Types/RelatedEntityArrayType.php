<?php

namespace Storm\Drivers\Base\Object\Properties\Types;

use \Storm\Core\Object;
use \Storm\Core\Object\Domain;
use \Storm\Core\Object\UnitOfWork;

class RelatedEntityArrayType extends RelatedType {
    private $BackReferenceProperty;
    private $CascadePersist;
    private $CascadeDiscard;
    private $ProxyGenerator;
    private $OriginalArrayKey;
    public function __construct(
            $EntityType,
            IProperty $BackReferenceProperty = null, 
            $CascadePersist = true, 
            $CascadeDiscard = false,
            Proxies\IProxyGenerator $ProxyGenerator = null) {
        parent::__construct($EntityType);
        $this->BackReferenceProperty = $BackReferenceProperty;
        $this->CascadePersist = $CascadePersist;
        $this->CascadeDiscard = $CascadeDiscard;
        $this->ProxyGenerator = $ProxyGenerator;
        $this->OriginalArrayKey = '__Original_' . $EntityType; 
    }
    
    public function ReviveValue(Domain $Domain, $Entity, $RevivalValue) {
        if(is_array($RevivalValue)) {
            if(count($RevivalValue) === 0) {
                return array();
            }
            
            $Entities = null;
            if(count(array_filter($RevivalValue, function ($Value) { return $Value instanceof Object\RevivalData; })) === count($RevivalValue)) {
                if($this->BackReferenceProperty !== null) {
                    foreach($RevivalValue as $RevivalData) {
                        $RevivalData[$this->BackReferenceProperty] = $Entity;
                    }
                }
                $Entities = $Domain->ReviveEntities($this->EntityType, $RevivalValue);
            }
            else if(count(array_filter($RevivalValue, function ($Value) { return is_callable($Value); })) === count($RevivalValue) && $this->ProxyGenerator !== null) {
                if($this->BackReferenceProperty !== null) {
                    foreach($RevivalValue as &$RevivalLoadFunction) {
                        $RevivalLoadFunction = function () use($RevivalLoadFunction, &$Entity) {
                            $RevivalData = $RevivalLoadFunction();
                            $RevivalData[$this->BackReferenceProperty] = $Entity;
                            
                            return $RevivalData;
                        };
                    }
                }
                $Entities = array();
                foreach($RevivalValue as $RevivalLoadFunction) {
                    $Entities[] = $this->ProxyGenerator->GenerateProxy($Domain, $this->EntityType, $RevivalLoadFunction);
                }
                
            }
            if(!$Entities !== null) {
                $Entity->{$this->OriginalArrayKey} = $Entities;
                return $Entities;
            }
        }
        else {
            throw new \Exception;//TODO: error message
        }
    }
    
    public function Persist(UnitOfWork $UnitOfWork, $Entity, $PropertyValue) {
        if($this->CascadePersist) {
            if(!is_array($PropertyValue)) {
                throw new \Exception;//TODO:error message
            }
            $OriginalArray = isset($Entity->{$this->OriginalArrayKey}) ? 
                    $Entity->{$this->OriginalArrayKey} : array();
            
            $PersistedData = array();
            $DiscardedIdentities = array();
            foreach(array_udiff($PropertyValue, $OriginalArray, [$this, 'ObjectComparison']) as $Entity) {
                $PersistedData[] = $UnitOfWork->Persist($Entity);
            }
            foreach(array_udiff($PropertyValue, $OriginalArray, [$this, 'ObjectComparison']) as $Entity) {
                $DiscardedIdentities[] = $UnitOfWork->Discard($Entity);
            }
            
            return new CollectionPersistanceData($PersistedData, $DiscardedIdentities);
        }
    }
    public function ObjectComparison(&$Object1, &$Object2) {
        return $Object1 == $Object2;
    }
    
    public function Discard(UnitOfWork $UnitOfWork, $Entity, $PropertyValue) {
        if($this->CascadeDiscard) {
            if(!is_array($PropertyValue)) {
                throw new \Exception;//TODO:error message
            }
            $Entities = isset($Entity->{$this->OriginalArrayKey}) ? 
                    $Entity->{$this->OriginalArrayKey} : $PropertyValue;
            
            $DiscardedIdentities = array();
            foreach($Entities as $Entity) {
                $DiscardedIdentities[] = $UnitOfWork->Discard($Entity);
            }
            
            return $DiscardedIdentities;
        }
    }
}

?>
