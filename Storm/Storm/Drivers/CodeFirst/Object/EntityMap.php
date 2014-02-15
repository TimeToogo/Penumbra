<?php

namespace Storm\Drivers\CodeFirst\Object;

use \Storm\Core\Object\IProperty;
use \Storm\Drivers\Base\Object;
use \Storm\Core\Containers\Registrar;
use \Storm\Drivers\Base\Object\Properties\Proxies;

abstract class EntityMap extends Object\EntityMap {
    private $Constructor;
    private $Properties = array();
    private $PropertyMetadata = array();
    private $ProxyGenerator;
    
    /**
     * @var Fluent\FullPropertyBuilder|null
     */
    private $PropertyBuilder;
    public function __construct() {
        parent::__construct();
    }
    
    final public function SetProxyGenerator(Proxies\IProxyGenerator $ProxyGenerator) {
        $this->ProxyGenerator = $ProxyGenerator;
    }
    
    /**
     * @return Metadata\Collection|null
     */
    final public function GetMetadata(IProperty $Property) {
        $Identifier = $Property->GetIdentifier();
        return isset($this->PropertyMetadata[$Identifier]) ? 
                $this->PropertyMetadata[$Identifier] : null;
    }

    final protected function EntityConstructor() {
        $this->DefineConstructor();
        return $this->Constructor;
    }
    protected function DefineConstructor() {
        $this->ConstructAs()->InstanceWithoutConstructor();
    }
    /**
     * Define the entity's construction type
     * 
     * @return Fluent\ConstructorDefinition
     */
    final protected function ConstructAs() {
        return new Fluent\ConstructorDefinition($this->Constructor);
    }

    final protected function RegisterProperties(\Storm\Core\Object\Domain $Domain, Registrar $Registrar) {
        $this->DefineProperties();
        $this->BuildLastProperty();
        $Registrar->RegisterAll($this->Properties);
    }
    
    protected abstract function DefineProperties();
    
    /**
     * @return Fluent\FullPropertyBuilder
     */
    final protected function PropertyOf() {
        if($this->PropertyBuilder !== null) {
            $this->BuildLastProperty();
        }
        
        $this->PropertyBuilder = new Fluent\FullPropertyBuilder($this->ProxyGenerator);
        return $this->PropertyBuilder;
    }
    
    private function BuildLastProperty() {
        $Property = $this->PropertyBuilder->BuildProperty();
        $Metadata = $this->PropertyBuilder->GetMetadata();
        $Property->SetEntityMap($this);
        $Identifier = $Property->GetIdentifier();
        $this->Properties[$Identifier] = $Property;
        $this->PropertyMetadata[$Identifier] = $Metadata;
    }
}

?>