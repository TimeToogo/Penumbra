<?php

namespace Penumbra\Drivers\Dynamic\Relational;

use \Penumbra\Core\Containers\Registrar;
use \Penumbra\Drivers\Base\Relational;

class Database extends Relational\Database {
    private $Tables;
    
    public function __construct(array $Tables) {
        $this->Tables = $Tables;
        parent::__construct();
    }
    
    final protected function RegisterTables(Registrar $Registrar) {
        $Registrar->RegisterAll($this->Tables);
    }
}

?>