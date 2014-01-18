<?php

namespace Storm\Drivers\Base\Object\Properties\Relationships;

use \Storm\Drivers\Base\Object\Properties\IRelationshipType;
use \Storm\Drivers\Base\Object\Properties\Proxies\IProxy;

abstract class RelationshipType implements IRelationshipType {
        
    final protected function IsEntityAltered($Entity) {
        if($Entity instanceof IProxy) {
            return $Entity->__IsAltered();
        }
        else {
            return true;
        }
    }
}


?>
