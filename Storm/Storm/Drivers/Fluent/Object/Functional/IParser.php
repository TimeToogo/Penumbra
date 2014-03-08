<?php

namespace Storm\Drivers\Fluent\Object\Functional;

use \Storm\Core\Object;
use \Storm\Core\Object\Expressions\Expression;

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
