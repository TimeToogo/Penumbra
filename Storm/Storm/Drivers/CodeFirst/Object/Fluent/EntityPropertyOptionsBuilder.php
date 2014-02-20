<?php

namespace Storm\Drivers\CodeFirst\Object\Fluent;

use \Storm\Drivers\Base\Object\Properties;
use \Storm\Drivers\CodeFirst\Object\Metadata;

class EntityPropertyOptionsBuilder extends LoadableRelationshipPropertyOptionsBuilder {
    private $IsOptional;
    public function __construct(&$IsOptional, &$RelationshipType, &$BackReferenceProperty) {
        parent::__construct($RelationshipType, $BackReferenceProperty);
        $this->IsOptional =& $IsOptional;
    }
    
    public function AsOptional() {
        $this->IsOptional = true;
    }
    
    public function AsRequired() {
        $this->IsOptional = false;
    }
}