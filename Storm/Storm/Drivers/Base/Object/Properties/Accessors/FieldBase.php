<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

abstract class FieldBase extends ReflectionBase {
    protected $FieldName;
    /**
     * @var \ReflectionProperty 
     */
    protected $Reflection;
    
    public function __construct($FieldName) {
        $this->FieldName = $FieldName;
    }

    final public function GetFieldName() {
        return $this->FieldName;
    }
    
    public function Identifier(&$Identifier) {
        $Identifier .= $this->FieldName;
    }
    
    public function __sleep() {
        return array_merge(parent::__sleep(), ['FieldName']);
    }
    
    final protected function LoadReflection() {
        $Reflection = new \ReflectionProperty($this->EntityType, $this->FieldName);
        $Reflection->setAccessible(true);
        
        return $Reflection;
    }
}

?>