<?php

namespace Storm\Drivers\Base\Mapping\Expressions;

use \Storm\Core\Object\Expressions as O;
use \Storm\Drivers\Base\Relational\Expressions as R;

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