<?php

namespace Storm\Drivers\CodeFirst\Object\Metadata;

use \Storm\Drivers\Base\Object;
use \Storm\Core\Containers\Registrar;

class EntitiesAreShareable extends Metadata {
    public static function AllowMultiple() {
        return false;
    }
}

?>