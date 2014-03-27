<?php

namespace Penumbra\Drivers\Platforms\Standard;

use \Penumbra\Drivers\Platforms;
use \Penumbra\Drivers\Base\Relational;
use \Penumbra\Drivers\Base\Mapping;

abstract class Platform extends Platforms\Base\Platform {
    public function __construct(
            Relational\IPlatform $RelationalPlatform, 
            Mapping\Expressions\IValueMapper $ValueMapper, 
            Mapping\Expressions\IArrayMapper $ArrayMapper, 
            Mapping\Expressions\IOperationMapper $OperationMapper, 
            Mapping\Expressions\IFunctionMapper $FunctionMapper, 
            Mapping\Expressions\IAggregateMapper $AggregateMapper, 
            Mapping\Expressions\IControlFlowMapper $ControlFlowMapper) {
        parent::__construct(
                $RelationalPlatform,
                $ValueMapper,
                $ArrayMapper,
                $OperationMapper,
                $FunctionMapper,
                $AggregateMapper,
                $ControlFlowMapper);
    }
}

?>