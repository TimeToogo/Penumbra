<?php

namespace Storm\Drivers\Base\Object\Properties\Types;

use \Storm\Core\Object\Domain;
use \Storm\Core\Object\RevivalData;
use \Storm\Core\Object\UnitOfWork;
use \Storm\Core\Object\IProperty;

class RelatedEntityType extends RelatedType {
    private $IsOptional;
    private $IsIdentifying;
    private $BackReferenceProperty;
    private $CascadePersist;
    private $CascadeDiscard;
    private $ProxyGenerator;
    private $OriginalEntityKey;
    public function __construct(
            $EntityType,
            $IsOptional = false,
            $IsIdentifying = true,
            IProperty $BackReferenceProperty = null, 
            $CascadePersist = true,
            $CascadeDiscard = false,
            Proxies\IProxyGenerator $ProxyGenerator = null) {
        parent::__construct($EntityType);
        $this->IsOptional = $IsOptional;
        $this->BackReferenceProperty = $BackReferenceProperty;
        $this->CascadePersist = $CascadePersist;
        $this->CascadeDiscard = $CascadeDiscard;
        $this->ProxyGenerator = $ProxyGenerator;
        $this->OriginalEntityKey = '__Original_' . $EntityType;
    }
    
    protected function ReviveNull(Domain $Domain, $Entity) {
        if($this->IsOptional) {
            return null;
        }
        else {
            throw new Exception;//TODO:error message
        }
    }
    
    protected function ReviveRevivalData(Domain $Domain, $Entity, RevivalData $RevivalData) {
        if($this->BackReferenceProperty !== null) {
            $RevivalData[$this->BackReferenceProperty] = $Entity;
        }
        if ($this->ProxyGenerator !== null) {
            $LoadFunction = static function () use (&$RevivalData) {
                return $RevivalData;
            };
            
            return $this->ProxyGenerator->GenerateProxy($Domain, $this->GetEntityType(), $LoadFunction);
        }
        else {
            reset($Domain->ReviveEntities($this->GetEntityType(), [$PropertyRevivalValue]));
        }
    }
    
    protected function ReviveCallable(Domain $Domain, $Entity, callable $Callback) {
        if ($this->ProxyGenerator !== null) {
            $LoadFunction = $Callback;
            if($this->BackReferenceProperty !== null) {
                $LoadFunction = function () use ($Callback, &$Entity) {
                    $RevivalData = $Callback();
                    $RevivalData[$this->BackReferenceProperty] = $Entity;
                    return $RevivalData;
                };
            }
            
            return $this->ProxyGenerator->GenerateProxy($Domain, $this->GetEntityType(), $LoadFunction);
        }
        else {
            throw new Exception;//TODO:error message
        }
    }
    
    public function Persist(UnitOfWork $UnitOfWork, $Entity, $PropertyValue) {
        if($this->CascadePersist) {
            if($PropertyValue instanceof Proxies\IProxy) {
                if(!$PropertyValue->__IsAltered()) {
                    return null;
                }
            }
            $ValidEntity = $PropertyValue instanceof $this->EntityType;
            if($ValidEntity) {
                return $UnitOfWork->Persist($PropertyValue);
            }
            else {
                if($this->IsOptional) {
                    return null;
                }
                else {
                    throw new Exception;//TODO: error mesage
                }
            }
        }
    }
    public function Discard(UnitOfWork $UnitOfWork, $Entity, $PropertyValue) {
        if($this->CascadeDiscard && $PropertyValue instanceof $this->EntityType) {
            return $UnitOfWork->Discard($PropertyValue);
        }
    }
}

?>
