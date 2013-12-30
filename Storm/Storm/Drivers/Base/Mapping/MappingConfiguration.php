<?php

namespace Storm\Drivers\Base\Mapping;

class MappingConfiguration implements IMappingConfiguration {
    private $DefaultLoadingMode;
    
    public function __construct($DefaultLoadingMode) {
        $this->DefaultLoadingMode = $DefaultLoadingMode;
    }
    
    final public function GetDefaultLoadingMode() {
        return $this->DefaultLoadingMode;
    }
}

?>