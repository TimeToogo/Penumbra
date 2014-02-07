<?php

namespace Storm\Core;

final class Utilities {
    public static function GetTypeOrClass($Value) {
        return is_object($Value) ? get_class($Value) : gettype($Value);
    }
}

?>
