<?php

namespace Storm\Drivers\CodeFirst\Object\Fluent;

use \Storm\Drivers\Base\Object\Properties;
use \Storm\Drivers\CodeFirst\Object\Metadata;

class CollectionPropertyBuilder extends RelationshipPropertyBuilder {
    private $IsArray;
    
    public function __construct(
            Properties\Accessors\Accessor $Accessor,
            $IsArray, 
            $EntityType, 
            Properties\Proxies\IProxyGenerator $ProxyGenerator) {
        parent::__construct(
                $Accessor,
                new CollectionPropertyOptionsBuilder($this->IsArray, $this->RelationshipType, $this->BackReferenceProperty), 
                $EntityType, 
                $ProxyGenerator);
        
        $this->IsArray = $IsArray;
    }
    
    /**
     * @return CollectionOptionsBuilder
     */
    final public function GetCollectionOptionsBuilder() {
        return $this->RelationshipOptions;
    }
    
    public function BuildProperty() {
        if($this->IsArray) {
            return new Properties\ArrayProperty(
                    $this->Accessor,
                    $this->EntityType, 
                    $this->RelationshipType, 
                    $this->BackReferenceProperty, 
                    $this->ProxyGenerator);
        }
        else {
            return new Properties\CollectionProperty(
                    $this->Accessor,
                    $this->EntityType, 
                    $this->RelationshipType, 
                    $this->BackReferenceProperty, 
                    $this->ProxyGenerator);
        }
    }
}