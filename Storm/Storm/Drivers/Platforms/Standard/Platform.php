<?php

namespace Storm\Drivers\Platforms\Standard;

use \Storm\Drivers\Platforms;
use \Storm\Drivers\Base\Relational;
use \Storm\Drivers\Base\Mapping;

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