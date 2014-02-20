<?php

namespace Storm\Drivers\CodeFirst\Object\Metadata;

use \Storm\Drivers\Base\Object;
use \Storm\Core\Containers\Registrar;

class MaxLength extends Metadata {
    public static function AllowMultiple() {
        return false;
    }
    
    /**
     * @var int
     */
    private $MaxLength;
    
    public function __construct($MaxLength) {
        $this->MaxLength = $MaxLength;
    }
    
    public function GetMaxLength() {
        return $this->MaxLength;
    }
}

?>