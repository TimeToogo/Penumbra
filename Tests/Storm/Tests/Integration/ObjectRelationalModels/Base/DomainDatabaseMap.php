<?php

namespace Storm\Tests\Integration\ObjectRelationalModels\Base;

use \Storm\Drivers\Constant\Mapping;
use \Storm\Drivers\Base\Relational\IPlatform;

abstract class DomainDatabaseMap extends Mapping\DomainDatabaseMap {
    private $Platform;
    public function __construct(IPlatform $Platform) {
        $this->Platform = $Platform;
        parent::__construct();
    }
    
    final protected function Database() {
        return $this->LoadDatabase($this->Platform);
    }
        
    protected abstract function LoadDatabase(IPlatform $Platform);
}

?>
