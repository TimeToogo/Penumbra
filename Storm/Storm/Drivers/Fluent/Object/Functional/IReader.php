<?php

namespace Storm\Drivers\Fluent\Object\Functional;

interface IReader {
    /**
     * @return \ReflectionFunctionAbstract
     */
    public function GetReflecton(callable $Function);
    
    /**
     * @return IData
     */
    public function Read(\ReflectionFunctionAbstract $Reflection);
}

?>
