<?php

namespace Storm\Drivers\Base\Object\Construction;

class ParameteratizedConstructor extends ReflectionConstructor {
    private $Parameters;
    public function __construct(array $Parameters) {
        $this->Parameters = $Parameters;
    }
    
    protected function ConstructFrom(\ReflectionClass $Reflection) {
        return $Reflection->newInstanceArgs($this->Parameters);
    }
}

?>
