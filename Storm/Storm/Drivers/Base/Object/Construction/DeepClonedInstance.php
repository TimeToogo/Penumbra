<?php

namespace Storm\Drivers\Base\Object\Construction;

class DeepClonedInstance implements IEntityConstructor {
    private $SerializedInstance;
    private $Instance;
    public function __construct($Instance) {
        $this->SerializedInstance = serialize($Instance);
        $this->Instance = $Instance;
    }
    
    public function Construct($EntityType) {
        if(!($this->Instance instanceof $EntityType))
            throw new \InvalidArgumentException('Unsupported Entity Type');
        else
            return unserialize($this->SerializedInstance);
    }
}

?>
