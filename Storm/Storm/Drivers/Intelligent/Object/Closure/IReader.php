<?php

namespace Storm\Drivers\Intelligent\Object\Closure;

interface IReader {
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
    
    public function GetBodySource();
}

?>
