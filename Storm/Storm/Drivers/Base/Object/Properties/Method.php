<?php

namespace Storm\Drivers\Base\Object\Properties;

abstract class Method {
    private $MethodName;
    protected $IsPublic;
    public function __construct($MethodName, $IsPublic = true) {
        $this->MethodName = $MethodName;
        $this->IsPublic = $IsPublic;
    }
    
    final public function GetMethodName() {
        return $this->MethodName;
    }
    
    final protected function ValidMethodOf($EntityType) {
        if(!method_exists($EntityType, $this->MethodName))
            return false;
        else if($this->IsPublic)
            return (new \ReflectionMethod($EntityType, $this->MethodName))->isPublic();
        else
            return true;
    }
    
    final protected function &GetReflectionMethod(&$Entity) {
        $Reflection = new \ReflectionMethod($Entity, $this->PropertyName);
        $Reflection->setAccessible(true);
        return $Reflection;
    }
}

?>