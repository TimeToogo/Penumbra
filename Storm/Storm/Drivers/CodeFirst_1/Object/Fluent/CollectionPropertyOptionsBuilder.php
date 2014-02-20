<?php

namespace Storm\Drivers\CodeFirst\Object\Fluent;

use \Storm\Drivers\Base\Object\Properties;
use \Storm\Drivers\CodeFirst\Object\Metadata;

class CollectionPropertyOptionsBuilder extends LoadableRelationshipPropertyOptionsBuilder {
    public function __construct(&$RelationshipType, &$BackReferenceProperty) {
        parent::__construct($RelationshipType, $BackReferenceProperty);
    }
    
    public function WithShareableEntities() {
        $this->Metadata->Add(new Metadata\EntitiesAreShareable());
    }
}