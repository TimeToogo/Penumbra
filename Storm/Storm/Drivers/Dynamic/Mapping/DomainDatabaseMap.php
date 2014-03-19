<?php

namespace Storm\Drivers\Dynamic\Mapping;

use \Storm\Core\Containers\Registrar;
use \Storm\Drivers\Base\Mapping;
use \Storm\Drivers\Base\Object;
use \Storm\Drivers\Base\Relational;

class DomainDatabaseMap extends Mapping\DomainDatabaseMap {
    private $Domain;
    private $Database;
    private $EntityRelationalMaps;
    
    public function __construct(
            Object\Domain $Domain, 
            Relational\Database $Database, 
            Mapping\IPlatform $Platform,
            array $EntityReltaionalMaps) {
        $this->Domain = $Domain;
        $this->Database = $Database;
        $this->EntityRelationalMaps = $EntityReltaionalMaps;
        
        parent::__construct($Platform);
    }
    
    final protected function Domain() {
        return $this->Domain;
    }
    
    final protected function Database() {
        return $this->Database;
    }

    final protected function RegisterEntityRelationalMaps(Registrar $Regisrar) {
        $Regisrar->RegisterAll($this->EntityRelationalMaps);
    }

}
?>
