<?php

namespace Storm\Drivers\Base\Object\Construction;

class ClonedInstance implements IEntityConstructor {
    private $Instance;
    public function __construct($Instance) {
        $this->Instance = $Instance;
    }
    
    public function Construct($EntityType) {
        if(!($this->Instance instanceof $EntityType))
            throw new \InvalidArgumentException('Unsupported EntityType');
        else
            return clone $this->Instance;
    }
}

?>
