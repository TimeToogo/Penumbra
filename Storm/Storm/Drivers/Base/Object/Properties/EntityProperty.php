<?php

namespace Storm\Drivers\Base\Object\Properties;

use \Storm\Core\Object;
use \Storm\Drivers\Base\Object\LazyRevivalData;

class EntityProperty extends RelationshipProperty implements Object\IEntityProperty {
    private $IsOptional;
    private $RelationshipType;
    public function __construct(
            Accessors\Accessor $Accessor,
            $EntityType,
            IRelationshipType $RelationshipType,
            $IsOptional = false,
            Object\IProperty $BackReferenceProperty = null,
            Proxies\IProxyGenerator $ProxyGenerator = null) {
        parent::__construct($Accessor, $EntityType, $RelationshipType->IsIdentifying(), $BackReferenceProperty, $ProxyGenerator);
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
            throw new \Storm\Core\UnexpectedValueException(
                    'Cannot revive entity property for %s, related entity %s is required',
                    $this->GetEntityMap()->GetEntityType(),
                    $this->GetEntityType());
        }
    }
    
    protected function ReviveRevivalData(Object\Domain $Domain, $Entity, Object\RevivalData $RevivalData) {
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
    protected function ReviveLazyRevivalData(Object\Domain $Domain, $Entity, LazyRevivalData $LazyRevivalData) {
        if ($this->ProxyGenerator !== null) {
            return $this->ProxyGenerator->GenerateProxy($Domain, $this->GetEntityType(), 
                    $LazyRevivalData->GetAlreadyKnownRevivalData(),
                    $LazyRevivalData->GetRevivalDataLoader());
        }
        else {
            throw $this->ProxyGeneratorIsRequired();
        }
    }
    
    private function InvalidEntityAndIsRequired($CurrentValue) {
        return new Object\ObjectException(
                'Invalid value for required relationship property on entity %s, %s expected, %s given',
                $this->GetEntityMap()->GetEntityType(),
                $this->GetEntityType(),
                \Storm\Core\Utilities::GetTypeOrClass($CurrentValue));
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
            throw $this->InvalidEntityAndIsRequired($CurrentValue);
        }
        else if(!$CurrentIsValidEntity && !$OriginalIsValidEntity) {
            
        }
        else if($CurrentValue == $OriginalValue) {
            $PersistedRelationship = $this->RelationshipType->GetPersistedRelationship(
                    $Domain, $UnitOfWork, 
                    $ParentEntity, $CurrentValue);
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
            throw $this->InvalidEntityAndIsRequired($CurrentValue);
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
