<?php

namespace Storm\Drivers\Base\Relational\Expressions\Converters;

use \Storm\Core\Relational;

interface IFunctionConverter {
    /**
     * @return Expression
     */
    public function MapFunctionCallExpression($FunctionName, array $ArgumentValueExpression = []);
}

?>