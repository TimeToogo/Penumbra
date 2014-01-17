<?php

namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Core\Relational;

interface IFunctionMapper {
    /**
     * @return Expression
     */
    public function MapFunctionCallExpression($FunctionName, array $ArgumentValueExpression = array());
}

?>