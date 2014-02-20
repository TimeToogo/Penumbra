<?php

namespace Storm\Drivers\CodeFirst\Object\Fluent;

use \Storm\Drivers\Base\Object\Properties;
use \Storm\Drivers\CodeFirst\Object\Metadata;

abstract class RelationshipPropertyBuilder extends PropertyBuilderBase {
    protected $EntityType;
    protected $RelationshipType;
    protected $BackReferenceProperty;
    protected $ProxyGenerator;
    
    public function __construct(
            Properties\Accessors\Accessor $Accessor,
            RelationshipPropertyOptionsBuilder $RelationshipOptions, 
            $EntityType, 
            Properties\Proxies\IProxyGenerator $ProxyGenerator) {
        parent::__construct($Accessor, $RelationshipOptions);
        $this->EntityType = $EntityType;
        $this->ProxyGenerator = $ProxyGenerator;
    }
}