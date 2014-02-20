<?php

namespace Storm\Drivers\CodeFirst\Mapping;

use \Storm\Drivers\Base\Mapping;
use \Storm\Drivers\CodeFirst\Object;
use \Storm\Core\Containers\Registrar;
use \Storm\Drivers\Base\Relational;

class DomainDatabaseMap extends Mapping\DomainDatabaseMap {
    private $Domain;
    private $Database;
    private $EntityRelationalMaps = array();
    
    public function __construct(Object\Domain $Domain, Relational\IPlatform $Platform) {
        parent::__construct();
    }
    
    protected function Database() {
        return $this->Database;
    }

    protected function Domain() {
        return $this->Domain;
    }

    protected function RegisterEntityRelationalMaps(Registrar $Registrar) {
        
    }

}

?>