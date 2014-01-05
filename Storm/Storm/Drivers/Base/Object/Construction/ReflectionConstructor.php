<?php

namespace Storm\Drivers\Base\Object\Construction;

abstract class ReflectionConstructor extends Constructor {
    private $Reflection;
    protected function OnSetEntityType($EntityType) {
        $this->Reflection = new \ReflectionClass($EntityType);
    }
    
    final public function Construct() {
        $this->ConstructFrom($this->Reflection);
    }
    protected abstract function ConstructFrom(\ReflectionClass $Reflection);
}

?>
