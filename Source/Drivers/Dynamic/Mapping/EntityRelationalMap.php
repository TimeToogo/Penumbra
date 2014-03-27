<?php

namespace Penumbra\Drivers\Dynamic\Mapping;

use \Penumbra\Core\Containers\Registrar;
use \Penumbra\Drivers\Base\Mapping;
use \Penumbra\Core\Object;
use \Penumbra\Core\Relational;

class EntityRelationalMap extends Mapping\EntityRelationalMap {
    private $EntityMap;
    private $PrimaryKeyTable;
    private $PropertyMappings;
    
    public function __construct(
            Object\IEntityMap $EntityMap,
            Relational\ITable $PrimaryKeyTable,
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
            Object\IEntityMap $EntityMap, Relational\Database $Database) {
        $Registrar->RegisterAll($this->PropertyMappings);
    }
}
?>
