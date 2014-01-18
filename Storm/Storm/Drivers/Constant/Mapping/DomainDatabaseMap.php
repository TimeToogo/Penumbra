<?php

namespace Storm\Drivers\Constant\Mapping;

use \Storm\Core\Containers\Registrar;
use \Storm\Drivers\Base\Mapping;

abstract class DomainDatabaseMap extends Mapping\DomainDatabaseMap {
    use \Storm\Drivers\Constant\Helpers\PropertyReflection;
    
    public function __construct() {
        $this->CreateRelationalMaps();
        parent::__construct();
    }
    protected abstract function CreateRelationalMaps();

    final protected function RegisterEntityRelationalMaps(Registrar $Registrar) {
        $this->LoadRegistrarFromProperties($Registrar);
    }

}
?>
