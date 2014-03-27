<?php

namespace Penumbra\Drivers\Base\Object\Construction;

use Penumbra\Core\Object;

class EmptyConstructor extends Constructor {
    protected function OnSetEntityType($EntityType) {
        
    }
    
    public function Construct(Object\RevivalData $RevivalData) {
        return new $this->EntityType();
    }
}

?>
