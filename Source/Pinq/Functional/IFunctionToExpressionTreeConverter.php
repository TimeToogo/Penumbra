<?php

namespace Penumbra\Pinq\Functional;

use \Penumbra\Core\Object;

/**
 * Converts functions to fully simplified and resolved expression trees.
 */
interface IFunctionToExpressionTreeConverter {
    /**
     * @return \ReflectionFunctionAbstract
     */
    public function GetReflection(callable $Function);
    
    /**
     * @return ExpressionTree
     */
    public function ConvertAndResolve(
            \ReflectionFunctionAbstract $Reflection,
            Object\IEntityMap $EntityMap,
            array $ParameterNameExpressionMap = []);
}

?>