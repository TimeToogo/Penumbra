<?php

namespace Penumbra\Drivers\Constant\Relational;

use \Penumbra\Core\Containers\Registrar;
use \Penumbra\Drivers\Base\Relational;

abstract class Database extends Relational\Database {
    use \Penumbra\Drivers\Constant\Helpers\PropertyReflection;
    
    public function __construct() {
        $this->CreateTables();
        parent::__construct();
    }
    protected abstract function CreateTables();
    
    final function RegisterTables(Registrar $Registrar) {
        $this->LoadRegistrarFromProperties($Registrar);
    }
}

?>
