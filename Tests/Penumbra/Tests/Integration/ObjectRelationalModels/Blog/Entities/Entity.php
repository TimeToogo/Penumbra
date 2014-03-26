<?php

namespace Penumbra\Tests\Integration\ObjectRelationalModels\Blog\Entities;

abstract class Entity {
    public static $Instances = [];
    public function __construct() {
        $Type = $this->GetType();
        if(!isset(self::$Instances[$Type]))
            self::$Instances[$Type] = 0;
        
        self::$Instances[$Type]++;
    }
    
    public function HelloWorld($Hi) {
        echo 'Hello' . $Hi;
    }
    
    public static function Instances() {
        $Type = static::GetType();
        return isset(self::$Instances[$Type]) ? self::$Instances[$Type] : 0;
    }
    
    public static function GetType() {
        return get_called_class();
    }
}

?>
