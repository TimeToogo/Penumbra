<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

use \Storm\Core\Object\UnitOfWork;
use \Storm\Core\Object\Domain;

interface IPropertyType {
    public function ReviveValue(Domain $Domain, $Entity, $PropertyRevivalValue);
    public function Persist(UnitOfWork $UnitOfWork, $Entity, $PropertyValue);
    public function Discard(UnitOfWork $UnitOfWork, $Entity, $PropertyValue);
}

?>
