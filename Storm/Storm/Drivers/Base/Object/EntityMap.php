<?php

namespace Storm\Drivers\Base\Object;

use \Storm\Core\Containers\Registrar;
use \Storm\Core\Object;
use \Storm\Drivers\Base\Object\Properties\Proxies\IProxyGenerator;

abstract class EntityMap extends Object\EntityMap {
    /**
     * @var IProxyGenerator 
     */
    private $ProxyGenerator;
    
    /**
     * @var Construction\IEntityConstructor 
     */
    private $EntityConstructor;
    
    public function __construct() {
        parent::__construct();
        $this->EntityConstructor = $this->EntityConstructor();
        if(!($this->EntityConstructor instanceof Construction\IEntityConstructor)) {
            throw new Object\ObjectException(
                    'The supplied entity constructor must implement %s: %s given',
                    Construction\IEntityConstructor::IEntityConstructorType,
                    \Storm\Core\Utilities::GetTypeOrClass($this->EntityConstructor));
        }
        if($this->EntityConstructor->HasEntityType()) {
            throw new Object\ObjectException(
                    'The supplied entity constructor %s already has an entity type %s',
                    get_class($this->EntityConstructor),
                    $this->EntityConstructor->GetEntityType());
        }
        $this->EntityConstructor->SetEntityType($this->GetEntityType());
    }
    
    final public function SetProxyGenerator(IProxyGenerator $ProxyGenerator) {
        $this->ProxyGenerator = $ProxyGenerator;
        $this->UpdateProxyGenerator();
    }
    
    final public function UpdateProxyGenerator() {
        foreach($this->GetProperties() as $Property) {
            if($Property instanceof Properties\RelationshipProperty) {
                $Property->SetProxyGenerator($this->ProxyGenerator);
            }
        }
    }
    
    final protected function GetProxyGenerator() {
        return $this->ProxyGenerator;
    }
    
    /**
     * @return Construction\IEntityConstructor
     */
    protected abstract function EntityConstructor();
    
    final public function ConstructEntity(Object\RevivalData $RevivalData) {
        return $this->EntityConstructor->Construct();
    }
    
    final protected function RegisterProperties(Object\Domain $Domain, Registrar $Registrar) {
        $ValueObjectRegistrar = new Registrar(Object\IEntityMap::IEntityMapType);
        $this->RegisterValueObjects($Domain, $ValueObjectRegistrar);
        foreach($ValueObjectRegistrar as $ValueObjectMap) {
            $ValueObjectMap->InitializeProperties($Domain);
            //TODO: traverse parent property
            //TODO: On ConstructEntity set parent property to the ConstructEntity of the value object map
            $Registrar->Register($ValueObjectMap->GetProperties());
        }
        
        $this->Properties($Domain, $Registrar);
    }
    protected abstract function Properties(Object\Domain $Domain, Registrar $Registrar);
    
    protected function RegisterValueObjects(Object\Domain $Domain, Registrar $Registrar) {}
}

?>
