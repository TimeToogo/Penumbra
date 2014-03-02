<?php

namespace Storm\Core\Helpers;

/**
 * This trait allows simple resolving of a class' or instance's type to a string.
 * 
 * NOTE: This will be replace by ...::class contant for PHP 5.5, although that does not support instances.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
trait Type {
    final public static function GetType() {
        return get_called_class();
    }
}

?>
