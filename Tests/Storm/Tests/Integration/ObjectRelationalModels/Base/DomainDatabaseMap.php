<?php

namespace Storm\Tests\Integration\ObjectRelationalModels\Base;

use \Storm\Drivers\Constant\Mapping;
use \Storm\Drivers\Base\Mapping\IPlatform;

abstract class DomainDatabaseMap extends Mapping\DomainDatabaseMap {
    
    public function __construct(IPlatform $Platform) {
        parent::__construct($Platform);
    }
    
    final protected function Database() {
        return $this->LoadDatabase();
    }
        
    protected abstract function LoadDatabase();
}

?>
