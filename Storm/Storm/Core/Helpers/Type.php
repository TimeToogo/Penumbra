<?php

namespace Storm\Core\Helpers;

trait Type {
    final public static function GetType() {
        return get_called_class();
    }
}

?>
