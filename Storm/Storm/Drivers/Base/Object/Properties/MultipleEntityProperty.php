<?php

namespace Storm\Drivers\Base\Object\Properties;

use \Storm\Core\Object;

abstract class MultipleEntityProperty extends RelationshipProperty implements Object\ICollectionProperty {
    /**
     * @var IRelationshipType 
     */
    protected $RelationshipType; 
    /**
     * @var Proxies\IProxyGenerator
     */
    protected $ProxyGenerator;
    
    public function __construct(
            Accessors\Accessor $Accessor,
            $EntityType,
            IRelationshipType $RelationshipType,
            Object\IProperty $BackReferenceProperty = null,
            Proxies\IProxyGenerator $ProxyGenerator = null) {
        parent::__construct($Accessor, $EntityType, $RelationshipType->IsIdentifying(), $BackReferenceProperty);
        
        $this->RelationshipType = $RelationshipType;
        $this->ProxyGenerator = $ProxyGenerator;
    }
    
    final function ReviveArrayOfCallables(Object\Domain $Domain, $Entity, array $Callbacks, Object\IProperty $BackReferenceProperty = null) {
        if($this->ProxyGenerator !== null) {
            if($BackReferenceProperty !== null) {
                foreach($Callbacks as $Key => $Callback) {
                    $Callbacks[$Key] = function() use($Callback, &$BackReferenceProperty, $Entity) {
                        $RevivalData = call_user_func_array($Callback, func_get_args());
                        $RevivalData[$BackReferenceProperty] = $Entity;
                        
                        return $RevivalData;
                    };
                }
            }
            $EntityType = $this->GetEntityType();
            $Proxies = $this->ProxyGenerator->GenerateProxies($Domain, $EntityType, $Callbacks);
            return $this->ReviveProxies($Proxies);
        }
        else {
            throw new \Exception;//TODO:error
        }
    }
    protected function ReviveProxies(Object\Domain $Domain, $Entity, array $Proxies) {
        throw new \Exception();
    }
    
    final protected function ReviveCallable(Object\Domain $Domain, $Entity, callable $Callback, Object\IProperty $BackReferenceProperty = null) {
        if($BackReferenceProperty !== null) {
            $Callback = function () use ($Callback, &$BackReferenceProperty, &$Entity) {
                $RevivalDataArray = call_user_func_array($Callback, func_get_args());
                foreach($RevivalDataArray as $RevivalData) {
                    $RevivalData[$BackReferenceProperty] = $Entity;
                }
                
                return $RevivalDataArray;
            };
        }
        return new Collections\LazyCollection($Domain, $this->GetEntityType(), $Callback);
    }
    protected function ReviveCallableProperty(Object\Domain $Domain, $Entity, callable $Callback) {
        throw new \Exception();
    }
    
    public function Persist(Object\UnitOfWork $UnitOfWork, $ParentEntity) {
        $Domain = $UnitOfWork->GetDomain();
        list(
                $CurrentValue, 
                $HasOriginalValue, 
                $OriginalValue) = $this->GetEntityRelationshipData($ParentEntity);
        
        return $this->PersistRelationshipChanges($Domain, $UnitOfWork, $ParentEntity, $CurrentValue, $HasOriginalValue, $OriginalValue);
    }
    protected abstract function PersistRelationshipChanges(
            Object\Domain $Domain, Object\UnitOfWork $UnitOfWork, $ParentEntity, $CurrentValue, $HasOriginalValue, $OriginalValue);
    
    final public function Discard(Object\UnitOfWork $UnitOfWork, $ParentEntity) {
        $Domain = $UnitOfWork->GetDomain();
        list(
                $CurrentValue, 
                $HasOriginalValue, 
                $OriginalValue) = $this->GetEntityRelationshipData($ParentEntity);
        
        return $this->DiscardRelationshipChanges($Domain, $UnitOfWork, $ParentEntity, $CurrentValue, $HasOriginalValue, $OriginalValue);
    }
    protected abstract function DiscardRelationshipChanges(
            Object\Domain $Domain, Object\UnitOfWork $UnitOfWork, $ParentEntity, $CurrentValue, $HasOriginalValue, $OriginalValue);
    
    
}

?>
