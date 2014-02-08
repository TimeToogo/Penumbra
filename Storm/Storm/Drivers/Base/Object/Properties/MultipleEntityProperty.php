<?php

namespace Storm\Drivers\Base\Object\Properties;

use \Storm\Core\Object;
use \Storm\Drivers\Base\Object\LazyRevivalData;
use \Storm\Drivers\Base\Object\MultipleLazyRevivalData;

abstract class MultipleEntityProperty extends RelationshipProperty implements Object\ICollectionProperty {
    /**
     * @var IRelationshipType 
     */
    protected $RelationshipType;
    
    public function __construct(
            Accessors\Accessor $Accessor,
            $EntityType,
            IRelationshipType $RelationshipType,
            Object\IProperty $BackReferenceProperty = null,
            Proxies\IProxyGenerator $ProxyGenerator = null) {
        parent::__construct($Accessor, $EntityType, $RelationshipType->IsIdentifying(), $BackReferenceProperty, $ProxyGenerator);
        
        $this->RelationshipType = $RelationshipType;
    }
    
    final protected function ReviveArrayOfLazyRevivalData(Object\Domain $Domain, $Entity, array $LazyRevivalDataArray, Object\IProperty $BackReferenceProperty = null) {
        if($this->ProxyGenerator !== null) {
            $EntityType = $this->GetEntityType();
            
            $AlreadyKnownRevivalData = array();
            $LoaderFunctions = array();
            array_walk($LazyRevivalDataArray,
                    function (LazyRevivalData $I, $Key) use (&$AlreadyKnownRevivalData, &$LoaderFunctions) { 
                        $AlreadyKnownRevivalData[$Key] = $I->GetAlreadyKnownRevivalData();
                        $LoaderFunctions[$Key] = $I->GetRevivalDataLoader(); 
                    });
                    
            $Proxies = $this->ProxyGenerator->GenerateProxies($Domain, $EntityType, $AlreadyKnownRevivalData, $LoaderFunctions);
            return $this->ReviveProxies($Domain, $Proxies, $Entity);
        }
        else {
            throw $this->ProxyGeneratorIsRequired();
        }
    }
    
    protected function ReviveProxies(Object\Domain $Domain, $Entity, array $Proxies) {
        throw new \Storm\Core\NotSupportedException(
                '%s cannot revive proxies, please override %s',
                get_class($this),
                __METHOD__);
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
