<?php

namespace Storm\Drivers\Pinq\Object\Functional;

use \Storm\Core\Object;
use \Storm\Core\Object\Expressions\Expression;

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
