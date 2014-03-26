<?php

namespace Penumbra\Drivers\Base\Object\Construction;

use \Penumbra\Core\Object;

class DeepClonedInstance extends Constructor {
    private $SerializedInstance;
    private $Instance;
    public function __construct($Instance) {
        $this->SerializedInstance = serialize($Instance);
        $this->Instance = $Instance;
    }
    
    protected function OnSetEntityType($EntityType) {
        if(!($this->Instance instanceof $EntityType)) {
            throw new Object\TypeMismatchException(
                    'Supplied entity type %s does not match the set entity type %s',
                    get_class($this->Instance),
                    $EntityType);
        }
    }
    
    public function Construct(Object\RevivalData $RevivalData) {
        return unserialize($this->SerializedInstance);
    }
}

?>
