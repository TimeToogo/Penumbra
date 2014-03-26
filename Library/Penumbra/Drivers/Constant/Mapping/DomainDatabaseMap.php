<?php

namespace Penumbra\Drivers\Constant\Mapping;

use \Penumbra\Core\Containers\Registrar;
use \Penumbra\Drivers\Base\Mapping;

abstract class DomainDatabaseMap extends Mapping\DomainDatabaseMap {
    use \Penumbra\Drivers\Constant\Helpers\PropertyReflection;
    
    public function __construct(Mapping\IPlatform $Platform) {
        $this->CreateRelationalMaps();
        parent::__construct($Platform);
    }
    protected abstract function CreateRelationalMaps();
    
    final protected function RegisterEntityRelationalMaps(Registrar $Registrar) {
        $this->LoadRegistrarFromProperties($Registrar);
    }

}
?>
