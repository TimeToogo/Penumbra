<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

abstract class FieldBase {
    private $FieldName;
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

    public function SetEntityType($EntityType) { 
        $this->Reflection = new \ReflectionProperty($EntityType, $this->FieldName);
        $this->Reflection->setAccessible(true);
    }   

}

?>