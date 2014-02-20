<?php

namespace Storm\Drivers\CodeFirst\Object\Metadata;

use \Storm\Drivers\Base\Object;
use \Storm\Core\Containers\Registrar;

class LoadingMode extends Metadata {
    public static function AllowMultiple() {
        return false;
    }
    
    private $LoadingMode;
    public function __construct($LoadingMode) {
        $this->LoadingMode = $LoadingMode;
    }
    
    public function GetDataType() {
        return $this->LoadingMode;
    }
}

?>