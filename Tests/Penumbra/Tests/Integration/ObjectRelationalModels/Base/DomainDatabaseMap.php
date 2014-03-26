<?php

namespace Penumbra\Tests\Integration\ObjectRelationalModels\Base;

use \Penumbra\Drivers\Constant\Mapping;
use \Penumbra\Drivers\Base\Mapping\IPlatform;

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
