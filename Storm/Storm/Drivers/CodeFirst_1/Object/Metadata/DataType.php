<?php

namespace Storm\Drivers\CodeFirst\Object\Metadata;

use \Storm\Drivers\Base\Object;
use \Storm\Core\Containers\Registrar;

class DataType extends Metadata {
    public static function AllowMultiple() {
        return false;
    }
    
    const String = 0;
    const Integer = 1;
    const Boolean = 2;
    const Double = 3;
    const Binary = 4;
    const DateTime = 5;
    
    private $DataType;
    public function __construct($DataType) {
        $this->DataType = $DataType;
    }
    
    public function GetDataType() {
        return $this->DataType;
    }
}

?>