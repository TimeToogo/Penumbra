<?php

namespace Storm\Drivers\Dynamic\Relational;

use \Storm\Core\Containers\Registrar;
use \Storm\Drivers\Base\Relational;

class Database extends Relational\Database {
    private $Platform;
    private $Tables;
    
    public function __construct(Relational\IPlatform $Platform, array $Tables) {
        $this->Platform = $Platform;
        $this->Tables = $Tables;
        
        parent::__construct();
    }
    protected function Platform() {
        return $this->Platform;
    }
    
    final protected function RegisterTables(Registrar $Registrar) {
        $Registrar->RegisterAll($this->Tables);
    }
}

?>