<?php

namespace Storm\Drivers\Fluent\Object;

use \Storm\Core\Object;
use \Storm\Core\Object\Expressions\ExpressionTree;

interface IFunctionToExpressionTreeConverter {
    /**
     * @return ExpressionTree
     */
    public function ConvertAndResolve(Object\IEntityMap $EntityMap, callable $Function);
}

?>