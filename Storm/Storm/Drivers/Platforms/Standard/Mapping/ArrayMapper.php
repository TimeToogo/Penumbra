<?php

namespace Storm\Drivers\Platforms\Standard\Mapping;

use \Storm\Drivers\Platforms\Base\Mapping;
use \Storm\Core\Object\Expressions as O;
use \Storm\Drivers\Base\Relational\Expressions as R;

class ArrayMapper extends Mapping\ArrayMapper {

    public function MapArrayExpression(
            array $MappedKeyExpressions, 
            array $MappedValueExpressions, 
            R\TraversalExpression $TraversalExpression = null) {
        
        if($TraversalExpression !== null) {
            throw new \Storm\Core\Mapping\MappingException(
                    '%s does not support mapping array traversal',
                    get_class($this));
        }
        
        //Keys are ignored
        return R\Expression::ValueList($MappedValueExpressions);
    }

}

?>