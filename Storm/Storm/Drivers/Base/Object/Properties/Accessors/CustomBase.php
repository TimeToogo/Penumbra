<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

class CustomBase {
    protected $Function;
    protected $Reflection;
    
    public function __construct(callable $Function) {
        $this->Function = $Function;
        $this->Reflection = new \ReflectionFunction($Function);
    }
    
    public function Identifier(&$Identifier) {
        $Identifier .= $this->Reflection->getFileName() . $this->Reflection->getStartLine() . $this->Reflection->getEndLine();
    }

    public function SetEntityType($EntityType) { }
    
    final protected function CallFunction(array $Arguments) {
        return $this->Reflection->invokeArgs($Arguments);
    }
}

?>
