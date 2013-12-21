<?php

namespace Storm\Drivers\Base\Object\Properties;

class Custom {
    protected $Function;
    protected $Reflection;
    protected $ParameterType = null;
    
    public function __construct(callable $Function) {
        $this->Function = $Function;
        $this->Reflection = new \ReflectionFunction($Function);
        $Parameters = $this->Reflection->getParameters();
        if(count($Parameters) > 0) {
            $Reflection = $Parameters[0]->getClass();
            $this->ParameterType = $Reflection ? $Reflection->getName() : null;
        }
    }
    
    final protected function ValidCustomOf($EntityType) {
        if($this->ParameterType === null)
            return true;
        else
            return is_subclass_of($EntityType, $this->ParameterType);
    }
    
    final protected function CallFunction(array $Arguments) {
        return $this->Reflection->invokeArgs($Arguments);
    }
}

?>
