<?php

namespace Storm\Drivers\Base\Object\Construction;

use Storm\Core\Object;

class EmptyConstructor extends Constructor {
    protected function OnSetEntityType($EntityType) {
        
    }
    
    public function Construct(Object\RevivalData $RevivalData) {
        return new $this->EntityType();
    }
}

?>
