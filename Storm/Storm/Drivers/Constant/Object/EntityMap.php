<?php

namespace Storm\Drivers\Constant\Object;

use \Storm\Core\Object\Domain as CoreDomain;
use \Storm\Core\Containers\Registrar;
use \Storm\Drivers\Base\Object;

abstract class EntityMap extends Object\EntityMap {
    use \Storm\Drivers\Constant\Helpers\PropertyReflection;
    
    public function __construct() {
        $this->CreateProperties();
        parent::__construct();
    }
    protected abstract function CreateProperties();
    protected function RegisterProperties(CoreDomain $Domain, Registrar $Registrar) {
        $this->LoadRegistrarFromProperties($Registrar);
    }
}

?>