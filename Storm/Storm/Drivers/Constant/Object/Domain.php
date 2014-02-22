<?php

namespace Storm\Drivers\Constant\Object;

use \Storm\Core\Containers\Registrar;
use \Storm\Drivers\Base\Object;

abstract class Domain extends Object\Domain {
    use \Storm\Drivers\Constant\Helpers\PropertyReflection;
    
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