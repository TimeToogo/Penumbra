<?php

namespace Storm\Drivers\CodeFirst\Relational;

use \Storm\Drivers\Base\Relational\IPlatform;
use \Storm\Drivers\Dynamic\Relational;
use \Storm\Drivers\Dynamic\Object\Domain;

class Database extends Relational\Database {
    public function __construct(Domain $Domain, IPlatform $Platform) {
        parent::__construct($Platform, $this->GenerateTables($Domain, $Platform));
    }
    
    /**
     * @return Table[]
     */
    private function GenerateTables(Domain $Domain, IPlatform $Platform) {
        $EntityMaps = $Domain->GetEntityMaps();
        $Tables = array();
        foreach($EntityMaps as $EntityMap) {
            $Tables[] = $this->GenerateTable($EntityMap, $Platform);
        }
        
        return $Tables;
    }
    
    private function GenerateTable(EntityMap $EntityMap, IPlatform $Platform) {
        
    }
}

?>