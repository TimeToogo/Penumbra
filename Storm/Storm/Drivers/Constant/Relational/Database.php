<?php

namespace Storm\Drivers\Constant\Relational;

use \Storm\Core\Containers\Registrar;
use \Storm\Drivers\Base\Relational;

abstract class Database extends Relational\Database {
    use \Storm\Drivers\Constant\Helpers\PropertyReflection;
    
    public function __construct(Relational\IPlatform $Platform) {
        $this->CreateTables();
        parent::__construct($Platform);
    }
    protected abstract function CreateTables();
    
    final function RegisterTables(Registrar $Registrar) {
        $this->LoadRegistrarFromProperties($Registrar);
    }
}

?>
