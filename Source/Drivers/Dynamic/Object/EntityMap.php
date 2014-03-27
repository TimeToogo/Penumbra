<?php

namespace Penumbra\Drivers\Dynamic\Object;

use \Penumbra\Core\Containers\Registrar;
use \Penumbra\Drivers\Base\Object;

class EntityMap extends Object\EntityMap {
    private $EntityType;
    private $EntityProperties;
    private $EntityConstructor;
    
    public function __construct($EntityType, array $EntityProperties, 
            Object\Construction\IConstructor $EntityConstructor) {
        $this->EntityType = $EntityType;
        $this->EntityProperties = $EntityProperties;
        $this->EntityConstructor = $EntityConstructor;
        parent::__construct();
    }
    
    protected function EntityConstructor() {
        return $this->EntityConstructor;
    }
    
    final protected function EntityType() {
        return $this->EntityType;
    }
    
   final protected function Properties(Registrar $Registrar) {
        $Registrar->RegisterAll($this->EntityProperties);
    }
}

?>