<?php

namespace Penumbra\Drivers\Dynamic\Object;

use \Penumbra\Core\Containers\Registrar;
use \Penumbra\Drivers\Base\Object;

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