<?php

namespace Storm\Drivers\Dynamic\Object;

use \Storm\Core\Containers\Registrar;
use \Storm\Drivers\Base\Object;

class Domain extends Object\Domain {
    private $EntityMaps;
    
    public function __construct(array $EntityMaps) {
        $this->EntityMaps = $EntityMaps;
        parent::__construct();
    }
    
    final protected function RegisterAllEntityMaps(Registrar $Registrar) {
        $Registrar->RegisterAll($this->EntityMaps);
    }
}

?>