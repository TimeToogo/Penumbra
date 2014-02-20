<?php

namespace Storm\Drivers\CodeFirst\Object\Fluent;

use \Storm\Drivers\Base\Object\Properties;
use \Storm\Drivers\CodeFirst\Object\Metadata;

class EntityPropertyBuilder extends RelationshipPropertyBuilder {
    private $IsOptional;
    
    public function __construct(
            Properties\Accessors\Accessor $Accessor,
            $EntityType, 
            Properties\Proxies\IProxyGenerator $ProxyGenerator) {
        parent::__construct(
                $Accessor,
                new EntityPropertyOptionsBuilder(
                        $this->IsOptional, $this->RelationshipType, $this->BackReferenceProperty), 
                $EntityType, 
                $ProxyGenerator);
    }
    
    /**
     * @return EntityPropertyOptionsBuilder
     */
    final public function GetEntityOptionsBuilder() {
        return $this->RelationshipOptions;
    }
    
    public function BuildProperty() {
        return new Properties\EntityProperty(
                $this->Accessor,
                $this->EntityType, 
                $this->RelationshipType, 
                $this->IsOptional, 
                $this->BackReferenceProperty, 
                $this->ProxyGenerator);
    }
}