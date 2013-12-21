<?php

namespace Storm\Drivers\Base\Object\Construction;

class CustomConstructor implements IEntityConstructor {
    private $ConstructorCallable;
    
    public function __construct(callable $ConstructorCallable) {
        $this->ConstructorCallable = $ConstructorCallable;
    }
    
    public function Construct($EntityType) {
        $Constructor = $this->ConstructorCallable;
        return $Constructor($EntityType);
    }
}

?>
