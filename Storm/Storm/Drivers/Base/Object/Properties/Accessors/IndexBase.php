<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

abstract class IndexBase {
    private $Index;
    public function __construct($Index) {
        $this->Index = $Index;
    }
    
    final public function GetIndex() {
        return $this->Index;
    }
    
    public function SetEntityType() { }
    
        
    public function Identifier(&$Identifier) {
        $Identifier .= $this->Index;
    }
}

?>
