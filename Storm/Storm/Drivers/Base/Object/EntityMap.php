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
     * @var Construction\IConstructor 
     */
    private $EntityConstructor;
    
    /**
     * @var Properties\EmbeddedObjectProperty[]
     */
    private $EmbeddedObjectProperties = [];
    
    public function __construct() {
        parent::__construct();
        $this->EntityConstructor = $this->EntityConstructor();
        if(!($this->EntityConstructor instanceof Construction\IConstructor)) {
            throw new Object\ObjectException(
                    'The supplied entity constructor must implement %s: %s given',
                    Construction\IConstructor::IConstructorType,
                    \Storm\Utilities\Type::GetTypeOrClass($this->EntityConstructor));
        }
        if($this->EntityConstructor->HasEntityType()) {
            throw new Object\ObjectException(
                    'The supplied entity constructor %s already has an entity type %s',
                    get_class($this->EntityConstructor),
                    $this->EntityConstructor->GetEntityType());
        }
        $this->EntityConstructor->SetEntityType($this->GetEntityType());
    }
    
    
    final public function GetProxyGenerator() {
        return $this->ProxyGenerator;
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
    
    /**
     * @return Construction\IConstructor
     */
    protected abstract function EntityConstructor();
    
    final public function ConstructEntity(Object\RevivalData $RevivalData) {
        $Entity = $this->EntityConstructor->Construct($RevivalData);
        foreach ($this->EmbeddedObjectProperties as $EmbeddedObjectProperty) {
            $EmbeddedObjectProperty->InitializeEmbeddedObject($Entity, $RevivalData);
        }
        
        return $Entity;
    }
    
    final protected function RegisterProperties(Object\Domain $Domain, Registrar $Registrar) {
        $PropertyRegistrar = new Registrar($Registrar->GetRegisterableType());
        $this->Properties($Domain, $PropertyRegistrar);
        
        foreach($PropertyRegistrar->GetRegistered() as $Property) {
            if($Property instanceof Properties\EmbeddedObjectProperty) {
                $Property->SetEntityType($this->GetEntityType());
                $this->EmbeddedObjectProperties[$Property->GetIdentifier()] = $Property;
                $Registrar->RegisterAll($Property->GetEmbeddedProperties());
            }
            else {
                $Registrar->Register($Property);
            }
        }
    }
    protected abstract function Properties(Object\Domain $Domain, Registrar $Registrar);
}

?>
