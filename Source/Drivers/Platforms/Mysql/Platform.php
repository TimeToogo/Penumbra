<?php

namespace Penumbra\Drivers\Platforms\Mysql;

use \Penumbra\Drivers\Base\Relational;
use \Penumbra\Drivers\Platforms\Standard;

final class Platform extends Standard\Platform {
    public function __construct($DevelopmentMode = false) {
        
        parent::__construct(
                new RelationalPlatform($DevelopmentMode),
                new Mapping\ValueMapper(),
                new Mapping\ArrayMapper(),
                new Mapping\OperationMapper(),
                new Mapping\FunctionMapper(),
                new Mapping\AggregateMapper(),
                new Standard\Mapping\ControlFlowMapper());
    }
    
    protected function TypeMappers() {
        return [
            new Mapping\Types\DateTimeMapper(),
            new Mapping\Types\DateTimeZoneMapper(),
            new Mapping\Types\DateIntervalMapper(),
        ];
    }
}

?>