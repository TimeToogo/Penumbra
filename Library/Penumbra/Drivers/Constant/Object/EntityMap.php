<?php

namespace Penumbra\Drivers\Constant\Object;

use \Penumbra\Core\Object\Domain as CoreDomain;
use \Penumbra\Core\Containers\Registrar;
use \Penumbra\Drivers\Base\Object;

abstract class EntityMap extends Object\EntityMap {
    use \Penumbra\Drivers\Constant\Helpers\PropertyReflection;
    
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