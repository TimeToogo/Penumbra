<?php

namespace Storm\Drivers\Fluent\Object\Closure;

interface IData {
    /**
     * @return \ReflectionFunction
     */
    public function GetReflection();
    
    /**
     * @return \ReflectionParameter[]
     */
    public function GetParameters();
    
    /**
     * @return array
     */
    public function GetUsedVariablesMap();
    
    public function GetSourceLines();
    
    public function GetSource();
    
    public function GetBodySourceLines();
    
}

?>
