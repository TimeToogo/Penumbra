<?php

namespace Storm\Drivers\Platforms\Standard\Mapping;

use \Storm\Drivers\Platforms\Base\Mapping;
use \Storm\Core\Object\Expressions as O;
use \Storm\Drivers\Base\Relational\Expressions as R;

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