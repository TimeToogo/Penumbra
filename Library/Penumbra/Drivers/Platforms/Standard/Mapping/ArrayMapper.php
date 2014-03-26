<?php

namespace Penumbra\Drivers\Platforms\Standard\Mapping;

use \Penumbra\Drivers\Platforms\Base\Mapping;
use \Penumbra\Core\Object\Expressions as O;
use \Penumbra\Drivers\Base\Relational\Expressions as R;

class ArrayMapper extends Mapping\ArrayMapper {

    public function MapArrayExpression(
            array $MappedKeyExpressions, 
            array $MappedValueExpressions, 
            R\TraversalExpression $TraversalExpression = null) {
        
        if($TraversalExpression !== null) {
            throw new \Penumbra\Core\Mapping\MappingException(
                    '%s does not support mapping array traversal',
                    get_class($this));
        }
        
        //Keys are ignored
        return R\Expression::ValueList($MappedValueExpressions);
    }

}

?>