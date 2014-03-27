<?php

namespace Penumbra\Drivers\Platforms\Standard\Mapping;

use \Penumbra\Drivers\Platforms\Base\Mapping;
use \Penumbra\Core\Object\Expressions as O;
use \Penumbra\Drivers\Base\Relational\Expressions as R;

class ControlFlowMapper extends Mapping\ControlFlowMapper {
    
    public function MapTernary(
            R\Expression $MappedConditionExpression, 
            R\Expression $MappedIfTrueExpression, 
            R\Expression $MappedIfFalseExpression) {
        
        return R\Expression::Conditional(
                $MappedConditionExpression, 
                $MappedIfTrueExpression, 
                $MappedIfFalseExpression);
    }

}

?>