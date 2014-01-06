<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

abstract class MethodBase {
    private $MethodName;
    protected $ConstantArguments;
    /**
     * @var \ReflectionMethod
     */
    protected $Reflection;
    
    public function __construct($MethodName, array $ConstantArguments = array()) {
        $this->MethodName = $MethodName;
        $this->ConstantArguments = $ConstantArguments;
    }
    
    final public function GetMethodName() {
        return $this->MethodName;
    }
    
    public function Identifier(&$Identifier) {
        $Identifier .= $this->MethodName;
    }

    public function SetEntityType($EntityType) { 
        $this->Reflection = new \ReflectionMethod($EntityType, $this->MethodName);
        $this->Reflection->setAccessible(true);
    }   
}

?>