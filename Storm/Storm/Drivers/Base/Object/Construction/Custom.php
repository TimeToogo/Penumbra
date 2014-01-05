<?php

namespace Storm\Drivers\Base\Object\Construction;

class Custom extends Constructor {
    private $ConstructorCallable;
    
    public function __construct(callable $ConstructorCallable) {
        $this->ConstructorCallable = $ConstructorCallable;
    }
    
    protected function OnSetEntityType($EntityType) {
        
    }
    
    public function Construct() {
        $Constructor = $this->ConstructorCallable;
        return $Constructor();
    }
}

?>
