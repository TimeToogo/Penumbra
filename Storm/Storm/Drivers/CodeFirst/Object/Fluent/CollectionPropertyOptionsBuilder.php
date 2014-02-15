<?php

namespace Storm\Drivers\CodeFirst\Object\Fluent;

use \Storm\Drivers\Base\Object\Properties;
use \Storm\Drivers\CodeFirst\Object\Metadata;

class CollectionPropertyOptionsBuilder extends LoadableRelationshipPropertyOptionsBuilder {
    public function __construct(&$RelationshipType, &$BackReferenceProperty) {
        parent::__construct($RelationshipType, $BackReferenceProperty);
    }
}