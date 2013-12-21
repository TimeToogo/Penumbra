<?php

namespace Storm\Drivers\Base\Object\Properties;

class Field {
    protected $Name;
    protected $IsPublic;
    public function __construct($FieldName, $IsPublic = true) {
        $this->Name = $FieldName;
        $this->IsPublic = $IsPublic;
    }

    final public function GetFieldName() {
        return $this->Name;
    }
    
    final protected function ValidPropertyOf($EntityType) {
        if(!property_exists($EntityType, $this->Name))
            return false;
        else if ($this->IsPublic)
            return (new \ReflectionProperty ($EntityType, $this->Name))->isPublic();
        else
            return true;
    }

    final protected function GetReflectionProperty($Entity) {
        $Reflection = new \ReflectionProperty($Entity, $this->Name);
        $Reflection->setAccessible(true);
        return $Reflection;
    }

}

?>