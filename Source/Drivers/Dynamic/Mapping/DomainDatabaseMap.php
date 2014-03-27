<?php

namespace Penumbra\Drivers\Dynamic\Mapping;

use \Penumbra\Core\Containers\Registrar;
use \Penumbra\Drivers\Base\Mapping;
use \Penumbra\Drivers\Base\Object;
use \Penumbra\Drivers\Base\Relational;

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
