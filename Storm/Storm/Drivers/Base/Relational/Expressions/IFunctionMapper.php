<?php

namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Core\Relational;
use \Storm\Core\Relational\Expressions\Expression;

interface IFunctionMapper {
    /**
     * @return Expression
     */
    public function MapFunctionCallExpression($FunctionName, array $ArgumentValueExpression = array());
}

?>