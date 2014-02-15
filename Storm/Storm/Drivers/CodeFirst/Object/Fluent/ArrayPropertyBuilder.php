<?php

namespace Storm\Drivers\CodeFirst\Object\Fluent;

use \Storm\Drivers\Base\Object\Properties;
use \Storm\Drivers\CodeFirst\Object\Metadata;

class ArrayPropertyBuilder extends RelationshipPropertyBuilder {
    public function __construct(
            Properties\Accessors\Accessor $Accessor,
            $EntityType, 
            Properties\Proxies\IProxyGenerator $ProxyGenerator) {
        parent::__construct(
                $Accessor,
                new ArrayPropertyOptionsBuilder($this->RelationshipType, $this->BackReferenceProperty), 
                $EntityType, 
                $ProxyGenerator);
    }
    
    /**
     * @return ArrayPropertyOptionsBuilder
     */
    final public function GetArrayOptionsBuilder() {
        return $this->RelationshipOptions;
    }
    
    public function BuildProperty() {
        return new Properties\ArrayProperty(
                $this->Accessor,
                $this->EntityType, 
                $this->RelationshipType, 
                $this->BackReferenceProperty, 
                $this->ProxyGenerator);
    }
}