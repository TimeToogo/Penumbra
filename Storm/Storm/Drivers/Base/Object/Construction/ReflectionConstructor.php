<?php

namespace Storm\Drivers\Base\Object\Construction;

abstract class ReflectionConstructor implements IEntityConstructor {
    private static $Cache = array();
    
    final public function Construct($EntityType) {
        if(!isset(self::$Cache[$EntityType]))
            self::$Cache[$EntityType] = new \ReflectionClass($EntityType);
        
        return $this->ConstructFrom(self::$Cache[$EntityType]);
    }
    protected abstract function ConstructFrom(\ReflectionClass $Reflection);
}

?>
