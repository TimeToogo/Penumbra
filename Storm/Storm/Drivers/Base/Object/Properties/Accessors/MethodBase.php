<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

abstract class MethodBase extends FunctionBase {
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
    
    final public function Identifier(&$Identifier) {
        $Identifier .= $this->Format($this->MethodName, $this->ConstantArguments);
    }
    
    public function SetEntityType($EntityType) { 
        $this->Reflection = new \ReflectionMethod($EntityType, $this->MethodName);
        $this->Reflection->setAccessible(true);
    }   
}

?>