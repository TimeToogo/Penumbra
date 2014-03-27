<?php

namespace Penumbra\Utilities;

final class Type {
    public static function GetTypeOrClass($Value) {
        return is_object($Value) ? get_class($Value) : gettype($Value);
    }
}

?>
