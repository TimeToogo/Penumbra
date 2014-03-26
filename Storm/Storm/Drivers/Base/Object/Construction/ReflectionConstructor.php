<?php

namespace Storm\Drivers\Base\Object\Construction;

use \Storm\Core\Object;

abstract class ReflectionConstructor extends Constructor {
    private $Reflections = [];
    protected function OnSetEntityType($EntityType) {
        if(isset($this->Reflections[$EntityType])) {
            return;
        }
        $this->Reflections[$EntityType] = new \ReflectionClass($EntityType);
    }
    
    final public function Construct(Object\RevivalData $RevivalData) {
        $this->ConstructFrom($this->Reflections[$this->EntityType]);
    }
    protected abstract function ConstructFrom(\ReflectionClass $Reflection);
}

?>
