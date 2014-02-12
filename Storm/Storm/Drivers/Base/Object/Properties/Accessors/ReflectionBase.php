<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

abstract class ReflectionBase {
    /**
     * @var string
     */
    protected $EntityType;
    /**
     * @var \IReflector
     */
    protected $Reflection;
    
    public function __sleep() {
        return ['EntityType'];
    }
    
    final public function __wakeup() {
        $this->Reflection = $this->LoadReflection();
    }
    
    final public function SetEntityType($EntityType) { 
        $this->EntityType = $EntityType;
        $this->Reflection = $this->LoadReflection();
    }
    
    
    /**
     * @return \IReflector
     */
    protected abstract function LoadReflection();
}

?>