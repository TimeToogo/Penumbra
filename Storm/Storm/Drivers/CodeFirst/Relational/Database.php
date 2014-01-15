<?php

namespace Storm\Drivers\CodeFirst\Relational;

use \Storm\Drivers\Dynamic\Relational;
use \Storm\Drivers\Base\Relational\IPlatform;
use \Storm\Drivers\Dynamic\Object\Domain;
use \Storm\Core\Containers\Registrar;

class Database extends Relational\Database {
    public function __construct(Domain $Domain, IPlatform $Platform) {
        parent::__construct($Platform, $this->GenerateTables($Domain));
    }
    
    /**
     * @return Table[]
     */
    private function GenerateTables(Domain $Domain) {
        
    }
    
    private function GenerateTable(EntityMap $EntityMap) {
        
    }
}

?>