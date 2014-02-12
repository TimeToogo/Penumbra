<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

abstract class IndexBase {
    /**
     * @var mixed
     */
    protected $Index;
    
    public function __construct($Index) {
        $this->Index = $Index;
    }
    
    /**
     * @return mixed
     */
    final public function GetIndex() {
        return $this->Index;
    }
    
    public function SetEntityType($EntityType) { }
    
        
    public function Identifier(&$Identifier) {
        $Identifier .= $this->Index;
    }
}

?>
