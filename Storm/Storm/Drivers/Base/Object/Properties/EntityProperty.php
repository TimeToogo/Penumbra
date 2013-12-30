<?php

namespace Storm\Drivers\Base\Object\Properties;

use \Storm\Core\Object;

class EntityProperty extends RelationshipProperty implements Object\IEntityProperty {
    private $IsOptional;
    private $ProxyGenerator;
    public function __construct(
            Accessors\Accessor $Accessor,
            $EntityType,
            $IsOptional = false,
            $IsIdentifying = true,
            Proxies\IProxyGenerator $ProxyGenerator = null) {
        parent::__construct($Accessor, $EntityType, $IsIdentifying);
        $this->IsOptional = $IsOptional;
        $this->ProxyGenerator = $ProxyGenerator;
    }

    final public function IsOptional() {
        return $this->IsOptional;
    }
        
    protected function RevivreNull(Object\Domain $Domain, $Entity) {
        if($this->IsOptional) {
            return null;
        }
        else {
            throw new Exception;//TODO:error message
        }
    }
    
    protected function ReviveRevivalData(Object\Domain $Domain, $Entity, RevivalData $RevivalData) {
        if ($this->ProxyGenerator !== null) {
            $LoadFunction = static function () use (&$RevivalData) {
                return $RevivalData;
            };
            
            return $this->ProxyGenerator->GenerateProxy($Domain, $this->GetEntityType(), $LoadFunction);
        }
        else {
            reset($Domain->ReviveEntities($this->GetEntityType(), [$RevivalData]));
        }
    }
    
    protected function ReviveCallable(Object\Domain $Domain, $Entity, callable $Callback) {
        if ($this->ProxyGenerator !== null) {
            return $this->ProxyGenerator->GenerateProxy($Domain, $this->GetEntityType(), $Callback);
        }
        else {
            throw new Exception;//TODO:error message
        }
    }
    
    public function Persist(Object\UnitOfWork $UnitOfWork, $ParentEntity) {
        $Domain = $UnitOfWork->GetDomain();
        list(
                $CurrentValue, 
                $HasOriginalValue, 
                $OriginalValue) = $this->GetEntityRelationshipData($ParentEntity);
        $OriginalIsValidEntity = $this->IsValidEntity($OriginalValue);
        $CurrentIsValidEntity = $this->IsValidEntity($CurrentValue);
        
        $PersistedRelationship = null;
        $DiscardedRelationship = null;
        
        if(!$CurrentIsValidEntity && !$this->IsOptional) {
            throw new Exception;//TODO:error message
        }
        else if(!$CurrentIsValidEntity && !$OriginalIsValidEntity) {
            
        }
        else if($CurrentValue == $OriginalValue || $Domain->ShareIdentity($CurrentValue, $OriginalValue)) {
            if($this->IsEntityAltered($CurrentValue)) {
                $UnitOfWork->Persist($CurrentValue);
            }
        }
        else {
            if($OriginalIsValidEntity) {
                if($this->IsIdentifying()) {
                    $UnitOfWork->Discard($OriginalValue);
                }
                $DiscardedRelationship = $Domain->Relationship($ParentEntity, $OriginalValue);
            }
            if($CurrentIsValidEntity) {
                if($this->IsEntityAltered($CurrentValue)) {
                    $UnitOfWork->Persist($CurrentValue);
                }
                $PersistedRelationship = $Domain->Relationship($ParentEntity, $CurrentValue);
            }
        }
        
        return new Object\RelationshipChange($PersistedRelationship, $DiscardedRelationship);
    }
    
    public function Discard(Object\UnitOfWork $UnitOfWork, $ParentEntity) {
        $Domain = $UnitOfWork->GetDomain();
        list(
                $CurrentValue, 
                $HasOriginalValue, 
                $OriginalValue) = $this->GetEntityRelationshipData($ParentEntity);
        $OriginalIsValidEntity = $this->IsValidEntity($OriginalValue);
        $CurrentIsValidEntity = $this->IsValidEntity($CurrentValue);
        
        $DiscardedRelationship = null;
        
        if(!$CurrentIsValidEntity && !$this->IsOptional) {
            throw new Exception;//TODO:error message
        }
        if($OriginalIsValidEntity) {
            if($this->IsIdentifying()) {
                $UnitOfWork->Discard($OriginalValue);
            }
            $DiscardedRelationship = $Domain->Relationship($ParentEntity, $OriginalValue);
        }
        
        return new Object\RelationshipChange(null, $DiscardedRelationship);
    }
}

?>
