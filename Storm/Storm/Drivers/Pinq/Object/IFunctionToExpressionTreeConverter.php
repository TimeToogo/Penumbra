<?php

namespace Storm\Drivers\Pinq\Object;

use \Storm\Core\Object;

/**
 * Converts functions to fully simplified and resolved expression trees.
 */
interface IFunctionToExpressionTreeConverter {
    /**
     * @return Functional\ExpressionTree
     */
    public function ConvertAndResolve(Object\IEntityMap $EntityMap, callable $Function, array $ParameterExpressionMap = []);
}

?>