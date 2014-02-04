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
    
    final public function SetProxyGenerator(Proxies\IProxyGenerator $ProxyGenerator) {
        $this->ProxyGenerator = $ProxyGenerator;
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
            throw new \Exception;//TODO:error
        }
    }
    
    protected function ReviveProxies(Object\Domain $Domain, $Entity, array $Proxies) {
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
