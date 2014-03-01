<?php

namespace Storm\Drivers\Constant\Object;

use \Storm\Core\Object\Domain as CoreDomain;
use \Storm\Core\Containers\Registrar;
use \Storm\Drivers\Base\Object;

abstract class EntityMap extends Object\EntityMap {
    use \Storm\Drivers\Constant\Helpers\PropertyReflection;
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function Properties(CoreDomain $Domain, Registrar $Registrar) {
        $this->CreateProperties($Domain);
        $this->LoadRegistrarFromProperties($Registrar);
    }
    protected abstract function CreateProperties(CoreDomain $Domain);
}

?>