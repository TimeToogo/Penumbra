<?php

namespace Storm\Drivers\Base\Object\Properties;

use \Storm\Core\Object;

class EntityProperty extends RelationshipProperty implements Object\IEntityProperty {
    private $IsOptional;
    private $RelationshipType;
    private $ProxyGenerator;
    public function __construct(
            Accessors\Accessor $Accessor,
            $EntityType,
            IRelationshipType $RelationshipType,
            $IsOptional = false,
            Object\IProperty $BackReferenceProperty = null,
            Proxies\IProxyGenerator $ProxyGenerator = null) {
        parent::__construct($Accessor, $EntityType, $RelationshipType->IsIdentifying(), $BackReferenceProperty);
        $this->IsOptional = $IsOptional;
        $this->RelationshipType = $RelationshipType;
        $this->ProxyGenerator = $ProxyGenerator;
    }
    
    public function IsOptional() {
        return $this->IsOptional;
    }
        
    protected function ReviveNull(Object\Domain $Domain, $Entity) {
        if($this->IsOptional) {
            return null;
        }
        else {
            throw new \Exception;//TODO:error message
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
            $RevivedEntities = $Domain->ReviveEntities($this->GetEntityType(), [$RevivalData]);
            return reset($RevivedEntities);
        }
    }
    
    protected function ReviveCallable(Object\Domain $Domain, $Entity, callable $Callback, Object\IProperty $BackReferenceProperty = null) {
        if ($this->ProxyGenerator !== null) {
            if($BackReferenceProperty !== null) {
                $Callback = function () use ($Callback, &$BackReferenceProperty, &$Entity) {
                    $RevivalData = call_user_func_array($Callback, func_get_args());
                    $RevivalData[$BackReferenceProperty] = $Entity;
                };
            }
            return $this->ProxyGenerator->GenerateProxy($Domain, $this->GetEntityType(), $Callback);
        }
        else {
            throw new \Exception;//TODO:error message
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
            throw new \Exception;//TODO:error message
        }
        else if(!$CurrentIsValidEntity && !$OriginalIsValidEntity) {
            
        }
        else if($CurrentValue == $OriginalValue) {
            
        }
        else if($Domain->DoShareIdentity($CurrentValue, $OriginalValue)) {
            $PersistedRelationship = $this->RelationshipType->GetPersistedRelationship(
                    $Domain, $UnitOfWork, 
                    $ParentEntity, $CurrentValue);
        }
        else {
            if($OriginalIsValidEntity) {
                $DiscardedRelationship = $this->RelationshipType->GetDiscardedRelationship(
                        $Domain, $UnitOfWork, 
                        $ParentEntity, $OriginalValue);
            }
            if($CurrentIsValidEntity) {
                $PersistedRelationship = $this->RelationshipType->GetPersistedRelationship(
                        $Domain, $UnitOfWork, 
                        $ParentEntity, $CurrentValue);
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
            throw new \Exception;//TODO:error message
        }
        if($OriginalIsValidEntity) {
            $DiscardedRelationship = $this->RelationshipType->GetDiscardedRelationship(
                    $Domain, $UnitOfWork, 
                    $ParentEntity, $OriginalValue);
        }
        
        return new Object\RelationshipChange(null, $DiscardedRelationship);
    }
}

?>
