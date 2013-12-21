<?php

namespace Storm\Drivers\Dynamic\Object;

use \Storm\Core\Containers\Registrar;
use \Storm\Drivers\Base\Object;

class EntityMap extends Object\EntityMap {
    private $EntityType;
    private $EntityProperties;
    private $EntityConstructor;
    
    public function __construct($EntityType, array $EntityProperties, 
            Object\Construction\IEntityConstructor $EntityConstructor) {
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
    
   final protected function RegisterProperties(Registrar $Registrar) {
        $Registrar->RegisterAll($this->EntityProperties);
    }
}

?>