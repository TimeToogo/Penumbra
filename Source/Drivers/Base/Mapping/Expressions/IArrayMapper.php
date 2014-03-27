<?php

namespace Penumbra\Drivers\Base\Mapping\Expressions;

use \Penumbra\Core\Object\Expressions as O;
use \Penumbra\Drivers\Base\Relational\Expressions as R;

interface IArrayMapper {
    
    /**
     * @return R\Expression
     */
    public function MapArrayExpression(
            array $MappedKeyExpressions, 
            array $MappedValueExpressions);
}

?>