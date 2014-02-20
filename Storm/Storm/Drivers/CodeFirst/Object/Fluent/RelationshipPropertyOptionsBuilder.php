<?php

namespace Storm\Drivers\CodeFirst\Object\Fluent;

use \Storm\Drivers\Base\Object\Properties;
use \Storm\Drivers\Base\Object\Properties\Relationships;
use \Storm\Drivers\Constant\Mapping\LoadingMode;
use \Storm\Drivers\CodeFirst\Object\Metadata;

class RelationshipPropertyOptionsBuilder extends PropertyOptionsBuilder {
    private $RelationshipType;
    
    private $BackReferenceProperty;
    
    public  function __construct(&$RelationshipType, &$BackReferenceProperty) {
        $this->RelationshipType =& $RelationshipType;
        $this->BackReferenceProperty =& $BackReferenceProperty;
    }
    
    public function AsIdentifying() {
        $this->RelationshipType = new Relationships\Identifying();
    }
    
    public function AsNonIdentifying() {
        $this->RelationshipType = new Relationships\NonIdentifying();
    }
    
    public function AsCascadeNonIdentifying() {
        $this->RelationshipType = new Relationships\CascadeNonIdentifying();
    }
    
    public function WithBackReference(\Storm\Core\Object\IProperty $Property) {
        $this->BackReferenceProperty = $Property;
    }
}