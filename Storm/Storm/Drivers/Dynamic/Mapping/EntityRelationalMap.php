<?php

namespace Storm\Drivers\Dynamic\Mapping;

use \Storm\Core\Containers\Registrar;
use \Storm\Drivers\Base\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

class EntityRelationalMap extends Mapping\EntityRelationalMap {
    private $EntityMap;
    private $Table;
    private $PropertyMappings;
    
    public function __construct(
            Object\EntityMap $EntityMap,
            array $PropertyMappings) {
        $this->EntityType = $EntityMap;
        $this->PropertyMappings = $PropertyMappings;
        
        parent::__construct();
    }

    protected function EntityMap(Object\Domain $Domain) {
        return $this->EntityMap;
    }
    
    protected function Table(Relational\Database $Database) {
        return $this->Table;
    }
    
    protected function RegisterPropertyMappings(Registrar $Registrar, 
            Object\EntityMap $EntityMap, Relational\Database $Database) {
        $Registrar->RegisterAll($this->PropertyMappings);
    }
}
?>
