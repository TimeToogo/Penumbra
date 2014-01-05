<?php

namespace Storm\Drivers\Base\Object\Construction;

class EmptyConstructor extends Constructor {
    private $EntityType;
    
    protected function OnSetEntityType($EntityType) {
        $this->EntityType = $EntityType;
    }
    
    public function Construct() {
        return new $this->EntityType();
    }
}

?>
