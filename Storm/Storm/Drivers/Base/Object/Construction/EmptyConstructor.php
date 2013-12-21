<?php

namespace Storm\Drivers\Base\Object\Construction;

class EmptyConstructor implements IEntityConstructor {
    public function Construct($EntityType) {
        return new $EntityType();
    }
}

?>
