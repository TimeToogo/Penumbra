<?php

namespace StormExamples\One\Entities;

abstract class Entity {    
    public static function GetType() {
        return get_called_class();
    }
}

?>
