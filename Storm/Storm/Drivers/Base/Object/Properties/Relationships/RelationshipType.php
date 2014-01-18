<?php

namespace Storm\Drivers\Base\Object\Properties\Relationships;

use \Storm\Drivers\Base\Object\Properties\IRelationshipType;
use \Storm\Core\Object;

abstract class RelationshipType implements IRelationshipType {
        
    final protected function IsEntityAltered($Entity) {
        if($Entity instanceof Proxies\IProxy) {
            return $Entity->__IsAltered();
        }
        else {
            return true;
        }
    }
}


?>
