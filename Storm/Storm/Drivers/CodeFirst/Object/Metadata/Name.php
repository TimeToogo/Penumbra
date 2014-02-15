<?php

namespace Storm\Drivers\CodeFirst\Object\Metadata;

use \Storm\Drivers\Base\Object;
use \Storm\Core\Containers\Registrar;

class Name extends Metadata {
    public static function AllowMultiple() {
        return false;
    }
    
    private $Name;
    
    public function __construct($Name) {
        $this->Name = $Name;
    }
    
    public function GetName() {
        return $this->Name;
    }
}

?>