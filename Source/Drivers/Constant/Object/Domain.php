<?php

namespace Penumbra\Drivers\Constant\Object;

use \Penumbra\Core\Containers\Registrar;
use \Penumbra\Drivers\Base\Object;

abstract class Domain extends Object\Domain {
    use \Penumbra\Drivers\Constant\Helpers\PropertyReflection;
    
    public function __construct() {
        $this->CreateEntityMaps();
        parent::__construct();
    }
    protected abstract function CreateEntityMaps();
    
    protected function RegisterAllEntityMaps(Registrar $Registrar) {
        $this->LoadRegistrarFromProperties($Registrar);
    }
}

?>