<?php

namespace Storm\Drivers\Base\Mapping\Expressions;

use \Storm\Core\Object\Expressions as O;
use \Storm\Drivers\Base\Relational\Expressions as R;

interface IArrayMapper {
    
    /**
     * @return R\Expression
     */
    public function MapArrayExpression(
            array $MappedKeyExpressions, 
            array $MappedValueExpressions);
}

?>