<?php

namespace Storm\Drivers\Base\Object\Properties;

use \Storm\Core\Object;
use \Storm\Drivers\Base\Object\LazyRevivalData;
use \Storm\Core\Object\Expressions as O;

class EntityProperty extends RelationshipProperty implements Object\IEntityProperty {
    private $IsOptional;
    private $IsIdentity;
    private $RelationshipType;
    public function __construct(
            Accessors\Accessor $Accessor,
            $EntityType,
            IRelationshipType $RelationshipType,
            $IsOptional = false,
            $IsIdentity = false,
            Object\IProperty $BackReferenceProperty = null,
            Proxies\IProxyGenerator $ProxyGenerator = null) {
        parent::__construct($Accessor, $EntityType, $RelationshipType->IsIdentifying(), $BackReferenceProperty, $ProxyGenerator);
        $this->IsOptional = $IsOptional;
        $this->IsIdentity = $IsIdentity;
        $this->RelationshipType = $RelationshipType;
        $this->ProxyGenerator = $ProxyGenerator;
    }
    
    public function IsIdentity() {
        return $this->IsIdentity;
    }
    
    public function IsOptional() {
        return $this->IsOptional;
    }
    
    protected function UpdateAccessor(Accessors\Accessor $Accessor) {
        return new self(
                $Accessor, 
                $this->RelatedEntityType,
                $this->RelationshipType,
                $this->BackReferenceProperty,
                $this->ProxyGenerator);
    }
    
    protected function ResolveExcessTraversal(O\TraversalExpression $ExcessTraversalExpression) {
        return $this->RelatedEntityMap->ResolveTraversalExpression($ExcessTraversalExpression);
    }
        
    protected function ReviveNull() {
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
    
    protected function ReviveEntity($Entity) {
        return $Entity;
    }
    
    protected function ReviveRevivalData(Object\RevivalData $RevivalData) {
        if ($this->ProxyGenerator !== null) {
            $LoadFunction = static function () use (&$RevivalData) {
                return $RevivalData;
            };
            
            return $this->ProxyGenerator->GenerateProxy($this->RelatedEntityMap, $LoadFunction);
        }
        else {
            $RevivedEntities = $this->RelatedEntityMap->ReviveEntities([$RevivalData]);
            return reset($RevivedEntities);
        }
    }
    protected function ReviveLazyRevivalData(LazyRevivalData $LazyRevivalData) {
        if ($this->ProxyGenerator !== null) {
            return $this->ProxyGenerator->GenerateProxy($this->RelatedEntityMap,
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
                \Storm\Utilities\Type::GetTypeOrClass($CurrentValue));
    }
    
    public function Persist(Object\UnitOfWork $UnitOfWork, $ParentEntity) {
        $Domain = $UnitOfWork->GetDomain();
        list(
                $CurrentValue, 
                $HasOriginalValue, 
                $OriginalValue) = $this->GetEntityRelationshipData($ParentEntity);
        
        $OriginalIsValidEntity = $this->IsValidEntity($OriginalValue);
        $CurrentIsValidEntity = $this->IsValidEntity($CurrentValue);
        
        $PersistedEntityData = null;
        $DiscardedIdentity = null;
        
        if(!$CurrentIsValidEntity && !$this->IsOptional) {
            throw $this->InvalidEntityAndIsRequired($CurrentValue);
        }
        else if(!$CurrentIsValidEntity && !$OriginalIsValidEntity) {
            
        }
        else if($CurrentValue == $OriginalValue) {
            $PersistedEntityData = $this->RelationshipType->GetPersistedEntityData(
                    $Domain, $UnitOfWork, $CurrentValue);
        }
        else if($Domain->DoShareIdentity($CurrentValue, $OriginalValue)) {
            $PersistedEntityData = $this->RelationshipType->GetPersistedEntityData(
                    $Domain, $UnitOfWork, $CurrentValue);
        }
        else {
            if($OriginalIsValidEntity) {
                $DiscardedIdentity = $this->RelationshipType->GetDiscardedIdentity(
                        $Domain, $UnitOfWork, $OriginalValue);
            }
            if($CurrentIsValidEntity) {
                $PersistedEntityData = $this->RelationshipType->GetPersistedEntityData(
                        $Domain, $UnitOfWork, $CurrentValue);
            }
        }
        
        return new Object\RelationshipChange($PersistedEntityData, $DiscardedIdentity);
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
            $DiscardedRelationship = $this->RelationshipType->GetDiscardedIdentity($Domain, $UnitOfWork, $OriginalValue);
        }
        
        return new Object\RelationshipChange(null, $DiscardedRelationship);
    }
}

?>