<?php

namespace Penumbra\Drivers\Base\Object\Construction;

class BlankInstance extends ReflectionConstructor {
    protected function ConstructFrom(\ReflectionClass $Reflection) {
        return $Reflection->newInstanceWithoutConstructor();
    }
}

?>
