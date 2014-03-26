<?php

namespace Penumbra\Drivers\Base\Mapping\Expressions;

use \Penumbra\Core\Object\Expressions as O;
use \Penumbra\Drivers\Base\Relational\Expressions as R;

interface IFunctionMapper {
    
    /**
     * @return R\Expression
     */
    public function MapFunctionCall(
            O\Expression $NameExpresssion, 
            array $MappedArgumentExpressions,
            &$ReturnType);
}

?>