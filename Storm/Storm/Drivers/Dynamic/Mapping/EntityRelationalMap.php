<?php

namespace Storm\Drivers\Dynamic\Mapping;

use \Storm\Core\Containers\Registrar;
use \Storm\Drivers\Base\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

class EntityRelationalMap extends Mapping\EntityRelationalMap {
    private $EntityMap;
    private $PrimaryKeyTable;
    private $PropertyMappings;
    
    public function __construct(
            Object\EntityMap $EntityMap,
            Relational\Table $PrimaryKeyTable,
            array $PropertyMappings) {
        $this->EntityMap = $EntityMap;
        $this->PrimaryKeyTable = $PrimaryKeyTable;
        $this->PropertyMappings = $PropertyMappings;
    }

    protected function EntityMap(Object\Domain $Domain) {
        return $this->EntityMap;
    }
    
    protected function PrimaryKeyTable(Relational\Database $Database) {
        return $this->PrimaryKeyTable;
    }
    
    protected function RegisterPropertyMappings(Registrar $Registrar, 
            Object\EntityMap $EntityMap, Relational\Database $Database) {
        $Registrar->RegisterAll($this->PropertyMappings);
    }
}
?>
