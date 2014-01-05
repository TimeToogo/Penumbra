<?php

namespace Storm\Drivers\Base\Object\Construction;

class DeepClonedInstance extends Constructor {
    private $SerializedInstance;
    private $Instance;
    public function __construct($Instance) {
        $this->SerializedInstance = serialize($Instance);
        $this->Instance = $Instance;
    }
    
    protected function OnSetEntityType($EntityType) {
        if(!($this->Instance instanceof $EntityType)) {
            throw new \InvalidArgumentException('Unsupported Entity Type');
        }
    }
    
    public function Construct() {
        return unserialize($this->SerializedInstance);
    }
}

?>
