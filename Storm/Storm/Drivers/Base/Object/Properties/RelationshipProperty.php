<?php

namespace Storm\Drivers\Base\Object\Properties;

use \Storm\Core\Object;
use \Storm\Core\Object\IEntityMap;
use \Storm\Core\Object\Expressions;
use \Storm\Drivers\Base\Object\LazyRevivalData;
use \Storm\Drivers\Base\Object\MultipleLazyRevivalData;
use \Storm\Drivers\Base\Object\Properties\Proxies\IProxyGenerator;

abstract class RelationshipProperty extends Property implements Object\IRelationshipProperty {
    /**
     * @var string
     */
    protected $RelatedEntityType;
    private $IsIdentifying;
    private $OriginalValueStorageKey;
    private $BackReferenceProperty;
    /**
     * @var IEntityMap
     */
    protected $RelatedEntityMap;
    /**
     * @var Proxies\IProxyGenerator|null
     */
    protected $ProxyGenerator;
    
    public function __construct(
            Accessors\Accessor $Accessor, 
            $EntityType, 
            $IsIdentifying,
            Object\IProperty $BackReferenceProperty = null,
            IProxyGenerator $ProxyGenerator = null) {
        parent::__construct($Accessor);
        $this->RelatedEntityType = $EntityType;
        $this->IsIdentifying = $IsIdentifying;
        $this->BackReferenceProperty = $BackReferenceProperty;
        $this->OriginalValueStorageKey = $EntityType . $this->GetIdentifier();
        $this->ProxyGenerator = $ProxyGenerator;
    }
    
    final public function HasProxyGenerator() {
        return $this->ProxyGenerator !== null;
    }
    
    final public function GetProxyGenerator() {
        return $this->ProxyGenerator;
    }
    final public function SetProxyGenerator(IProxyGenerator $ProxyGenerator) {
        $this->ProxyGenerator = $ProxyGenerator;
    }
        
    final public function GetRelatedEntityType() {
        return $this->RelatedEntityType;
    }
    
    final public function IsIdentifying() {
        return $this->IsIdentifying;
    }
    
    public function GetRelatedEntityMap() {
        return $this->RelatedEntityMap;
    }
    
    public function SetRelatedEntityMap(IEntityMap $EntityMap) {
        if($EntityMap->GetEntityType() !== $this->RelatedEntityType) {
            throw new Object\ObjectException(
                    'Expecting entity map for entity %s: %s given',
                    $this->RelatedEntityType,
                    $EntityMap->GetEntityType());
        }
        $this->RelatedEntityMap = $EntityMap;
    }
    
    public function ParseTraversalExpression(Expressions\TraversalExpression $Expression, Expressions\PropertyExpression $ParentPropertyExpression = null) {
        while (!($Expression instanceof Expressions\EntityExpression)) {
            $PropertyExpression = parent::ParseTraversalExpression($Expression, $ParentPropertyExpression);
            if($PropertyExpression !== null) {
                return $this->RelatedEntityMap->ParseTraversalExpression($Expression, $PropertyExpression);
            }
            $Expression = $Expression->GetValueExpression();
        }
    }
    
    /**
     * @return Object\IProperty
     */
    final public function GetBackReferenceProperty() {
        return $this->BackReferenceProperty;
    }
    
    final protected function ProxyGeneratorIsRequired() {
        return new \Storm\Core\NotSupportedException(
                'Cannot revive %s property for %s, related entity %s in lazy context: no proxy generator has been set',
                get_class($this),
                $this->GetEntityMap()->GetEntityType(),
                $this->GetEntityType());
    }


    final public function Revive($PropertyValue, $Entity) {
        $RevivedPropertyValue = $this->ReviveValue($Entity, $PropertyValue);
        if($RevivedPropertyValue instanceof Proxies\IProxy) {
            $Entity->{$this->OriginalValueStorageKey} = $RevivedPropertyValue->__CloneProxyInstance();
        }
        else {
            $Entity->{$this->OriginalValueStorageKey} = is_object($RevivedPropertyValue) ?
                    clone $RevivedPropertyValue : $RevivedPropertyValue;
        }
        $this->GetAccessor()->SetValue($Entity, $RevivedPropertyValue);
    }
    
    final public function ReviveValue($Entity, $PropertyRevivalValue) {
        if($PropertyRevivalValue === null) {
            return $this->ReviveNull();
        }
        if($PropertyRevivalValue instanceof Object\RevivalData) {
            $this->AddBackReference($PropertyRevivalValue, $Entity);
            return $this->ReviveRevivalData($PropertyRevivalValue);
        }
        if($PropertyRevivalValue instanceof LazyRevivalData) {
            $this->AddBackReference($PropertyRevivalValue->GetAlreadyKnownRevivalData(), $Entity);
            return $this->ReviveLazyRevivalData($PropertyRevivalValue);
        }
        if($PropertyRevivalValue instanceof MultipleLazyRevivalData) {
            $this->AddBackReference($PropertyRevivalValue->GetAlreadyKnownRevivalData(), $Entity);
            return $this->ReviveMultipleLazyRevivalData($PropertyRevivalValue);
        }
        else if(is_array($PropertyRevivalValue)) {
            if($this->IsAll($PropertyRevivalValue, function ($I) { return $I instanceof Object\RevivalData; })) {
                $this->AddBackReferences($PropertyRevivalValue, $Entity);
                return $this->ReviveArrayOfRevivalData($PropertyRevivalValue);
            }
            if($this->IsAll($PropertyRevivalValue, function ($I) { return $I instanceof LazyRevivalData; })) {
                foreach($PropertyRevivalValue as $LazyRevivalData) {
                    $this->AddBackReference($LazyRevivalData->GetAlreadyKnownRevivalData(), $Entity);
                }
                return $this->ReviveArrayOfLazyRevivalData($PropertyRevivalValue);
            }
        }
        
        throw new \Storm\Core\UnexpectedValueException(
                '%s cannot revive supplied value: %s given',
                __CLASS__,
                \Storm\Core\Utilities::GetTypeOrClass($PropertyRevivalValue));
    }
    
    private function IsAll(array $Values, callable $Filter) {
        return count(array_filter($Values, $Filter)) === count($Values);
    }
    
    private function AddBackReference(Object\RevivalData $RevivalData, $ParentEntity) {
        if($this->BackReferenceProperty !== null) {
            $RevivalData[$this->BackReferenceProperty] = $ParentEntity;
        }
    }
    private function AddBackReferences(array $RevivalDataArray, $ParentEntity) {
        if($this->BackReferenceProperty !== null) {
            foreach($RevivalDataArray as $RevivalData) {
                $this->AddBackReference($RevivalData, $ParentEntity);
            }
        }
    }
    
    private function Unimplemented($Method) {
        return new \Storm\Core\NotSupportedException(
                'Cannot revive %s property for %s, related type %s: %s must be implemented',
                get_class($this),
                $this->GetEntityMap()->GetEntityType(),
                $this->GetEntityType(),
                $Method);
    }
    
    protected function ReviveNull() {
        throw $this->Unimplemented(__METHOD__);
    }
    
    protected function ReviveRevivalData(Object\RevivalData $RevivalData) {
        throw $this->Unimplemented(__METHOD__);
    }
    
    protected function ReviveLazyRevivalData(LazyRevivalData $LazyRevivalData) {
        throw $this->Unimplemented(__METHOD__);
    }
    
    protected function ReviveMultipleLazyRevivalData(MultipleLazyRevivalData $MultipleLazyRevivalData) {
        throw $this->Unimplemented(__METHOD__);
    }
    
    protected function ReviveArrayOfRevivalData(array $RevivalDataArray) {
        throw $this->Unimplemented(__METHOD__);
    }
    
    protected function ReviveArrayOfLazyRevivalData(array $LazyRevivalDataArray) {
        throw $this->Unimplemented(__METHOD__);
    }
    
    
    final protected function GetEntityRelationshipData($ParentEntity) {
        return [
                $this->GetAccessor()->GetValue($ParentEntity),
                $this->HasOriginalValue($ParentEntity),
                $this->GetOriginalValue($ParentEntity),
            ];
    }
    
    final protected function IsValidEntity($Entity) {
        return $Entity instanceof $this->RelatedEntityType;
    }
    
    final protected function GetOriginalValue($Entity) {
        return property_exists($Entity, $this->OriginalValueStorageKey) ? $Entity->{$this->OriginalValueStorageKey} : null;
    }
    
    final protected function HasOriginalValue($Entity) {
        return property_exists($Entity, $this->OriginalValueStorageKey);
    }
}

?>
