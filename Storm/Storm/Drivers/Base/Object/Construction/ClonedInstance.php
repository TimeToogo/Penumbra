<?php

namespace Storm\Drivers\Base\Object\Construction;

class ClonedInstance extends Constructor {
    private $Instance;
    public function __construct($Instance) {
        $this->Instance = $Instance;
    }
    
    protected function OnSetEntityType($EntityType) {
        if(!($this->Instance instanceof $EntityType)) {
            throw new \InvalidArgumentException('Unsupported EntityType');
        }
    }
    public function Construct() {
        return clone $this->Instance;
    }
}

?>
