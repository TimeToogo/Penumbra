<?php

namespace Storm\Drivers\Fluent\Object\Functional;

interface IReader {
    /**
     * @return \ReflectionFunctionAbstract
     */
    public function GetReflection(callable $Function);
    
    /**
     * @return IData
     */
    public function Read(\ReflectionFunctionAbstract $Reflection);
}

?>
