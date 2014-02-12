<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

abstract class MethodBase extends ReflectionBase {
    protected $MethodName;
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
    
    final protected function Format($FunctionName, array $Arguments) {
        return sprintf('%s(%s)', 
                $FunctionName,
                implode(', ', 
                        array_map(
                            function ($Value) { 
                                return var_export($Value, true); 
                            },
                            $Arguments)));
    }
    
    public function __sleep() {
        return array_merge(parent::__sleep(), ['MethodName', 'ConstantArguments']);
    }
    
    protected function LoadReflection() {
        $Reflection = new \ReflectionMethod($this->EntityType, $this->MethodName);
        $Reflection->setAccessible(true);
        
        return $Reflection;
    }
}

?>