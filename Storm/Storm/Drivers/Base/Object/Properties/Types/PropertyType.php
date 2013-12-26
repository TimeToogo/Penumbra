<?php

namespace Storm\Drivers\Base\Object\Properties\Types;

use \Storm\Drivers\Base\Object\Properties\IPropertyType;
use \Storm\Core\Object\Domain;
use \Storm\Core\Object\UnitOfWork;

abstract class PropertyType implements IPropertyType {
    public function ReviveValue(Domain $Domain, $Entity, $PropertyRevivalValue) {
        return $RevivalValue;
    }
    
    public function Persist(UnitOfWork $UnitOfWork, $Entity, $PropertyValue) { 
        return $PropertyValue;
    }
    public function Discard(UnitOfWork $UnitOfWork, $Entity, $PropertyValue) { }
}

?>
