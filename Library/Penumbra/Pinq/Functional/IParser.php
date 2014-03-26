<?php

namespace Penumbra\Pinq\Functional;

use \Penumbra\Core\Object;
use \Penumbra\Core\Object\Expressions\Expression;

/**
 * The IParser is just an abstraction for converting a function to an ast structure
 * which can than be converted to an expression tree.
 */
interface IParser {
    
    /**
     * @return \ReflectionFunctionAbstract
     */
    public function GetReflection(callable $Function);
    
    /**
     * @return IAST
     */
    public function Parse(\ReflectionFunctionAbstract $Reflection);
}

?>
