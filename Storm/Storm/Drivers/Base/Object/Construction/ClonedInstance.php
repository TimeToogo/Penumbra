<?php

namespace Storm\Drivers\Base\Object\Construction;

use \Storm\Core\Object;

class ClonedInstance extends Constructor {
    private $Instance;
    public function __construct($Instance) {
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
    public function Construct() {
        return clone $this->Instance;
    }
}

?>
