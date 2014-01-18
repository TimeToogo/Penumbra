<?php

namespace Storm\Drivers\Dynamic\Mapping;

use \Storm\Core\Containers\Registrar;
use \Storm\Drivers\Base\Mapping;
use \Storm\Drivers\Base\Object;
use \Storm\Drivers\Base\Relational;

class DomainDatabaseMap extends Mapping\DomainDatabaseMap {
    private $Domain;
    private $Database;
    private $MappingConfiguration;
    private $EntityMaps;
    
    public function __construct(
            Object\Domain $Domain, 
            Relational\Database $Database, 
            Mapping\IMappingConfiguration $MappingConfiguration,
            array $EntityMaps) {
        $this->Domain = $Domain;
        $this->Database = $Database;
        $this->MappingConfiguration = $MappingConfiguration;
        $this->EntityMaps = $EntityMaps;
        
        parent::__construct();
    }
    
    final protected function MappingConfiguration() {
        return $this->MappingConfiguration;
    }
    
    final protected function Domain() {
        return $this->Domain;
    }
    
    final protected function Database() {
        return $this->Database;
    }

    final protected function RegisterEntityRelationalMaps(Registrar $Regisrar) {
        $Regisrar->RegisterAll($this->EntityMaps);
    }

}
?>
