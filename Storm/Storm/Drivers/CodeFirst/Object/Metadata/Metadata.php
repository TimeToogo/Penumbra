<?php

namespace Storm\Drivers\CodeFirst\Object\Metadata;

use \Storm\Drivers\Base\Object;
use \Storm\Core\Containers\Registrar;

abstract class Metadata {
    use \Storm\Core\Helpers\Type;
    public static function AllowMultiple() {
        return true;
    }
}

?>