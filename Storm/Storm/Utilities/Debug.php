<?php

namespace Storm\Utilities;

final class Unsetter {
    private function __construct() {}
    private static $Instance;
    private static $Unsetter;
    
    private function Make($Object, \ReflectionProperty $Property) {
        if(self::$Unsetter === null) {
            self::$Unsetter = function ($Name) {
                unset($this->$Name);
            };
        }
        $Unsetter = clone self::$Unsetter;
        return static function() use($Object, $Property, $Unsetter) {
            $Unsetter = $Unsetter->bindTo($Object, $Property->class);
            $Unsetter($Property->name);
        };
    }
    
    public static function UnsetProperty($Object, \ReflectionProperty $Property) {
        if(self::$Instance === null) {
            self::$Instance = new self();
        }
        $Unsetter = self::$Instance->Make($Object, $Property);
        $Unsetter();
    }
}

final class Debug {
    private static $ReplacementTypes;
    private static function ReplacementTypes() {
        if(self::$ReplacementTypes === null) {
            self::$ReplacementTypes = [
                \Storm\Drivers\Base\Object\Properties\Proxies\IProxy::IProxyType => function (
                        \Storm\Drivers\Base\Object\Properties\Proxies\IProxy $Proxy, \ReflectionObject $Reflection, array $TypeReplacementMap, $MaxDepth, $Seen) {
                    $PropertiesToUnset = [];
                    foreach($Reflection->getProperties() as $Property) {
                        if($Property->getDeclaringClass()->implementsInterface(\Storm\Drivers\Base\Object\Properties\Proxies\IProxy::IProxyType)) {
                            $PropertiesToUnset[] = $Property;
                        }
                        else {
                            self::ReplaceProperty($Proxy, $Property, $TypeReplacementMap, $MaxDepth, $Seen);
                        }
                    }
                    foreach($PropertiesToUnset as $Property) {
                        self::UnsetProperty($Proxy, $Property);
                    }
                    
                    return $Proxy;
                },
                \Storm\Core\Object\IEntityMap::IEntityMapType => null,
                \Storm\Drivers\Base\Object\Properties\Proxies\IProxyGenerator::IProxyGeneratorType => null,
            ];
        }
        
        return self::$ReplacementTypes;
    }
    
    private function __construct() {}
    
    public static function Dump($Value, $MaxDepth = 5) {
        $Value = self::ReplaceTypes($Value, self::ReplacementTypes(), $MaxDepth);
        var_dump($Value);
    }
    
    private static function ReplaceProperty($Value, \ReflectionProperty $Property, array $TypeReplacementMap, $MaxDepth, \SplObjectStorage $Seen = null) {
        $Property->setAccessible(true);
        $PropertyValue = $Property->getValue($Value);
        
        $Property->setValue($Value, self::ReplaceTypes($PropertyValue, $TypeReplacementMap, $MaxDepth, $Seen));
    }
    
    private static function UnsetProperty(&$Value, \ReflectionProperty $Property) {
        Unsetter::UnsetProperty($Value, $Property);
    }
    
    private static function ReplaceTypes($Value, array $TypeReplacementMap, $MaxDepth, \SplObjectStorage $Seen = null) {
        if($MaxDepth <= 0) {
            return null;
        }
        if($Seen === null) {
            $Seen = new \SplObjectStorage();
        }
        if(is_array($Value)) {
            $Values = [];
            foreach ($Value as $Key => $Item) {
                $Values[$Key] = self::ReplaceTypes($Item, $TypeReplacementMap, $MaxDepth - 1, $Seen);
            }
            
            return $Values;
        }
        else if(is_object($Value)) {
            if($Seen->contains($Value)) {
                return '*RECURSION*';
            }
            $Seen->attach($Value);
            $Reflection = new \ReflectionObject($Value);
            $Value = $Reflection->isCloneable() ? clone $Value : $Value;
            
            foreach($TypeReplacementMap as $Type => $Replacement) {
                if($Value instanceof $Type) {
                    return $Replacement instanceof \Closure ? $Replacement($Value, $Reflection, $TypeReplacementMap, $MaxDepth - 1, $Seen) : $Replacement;
                }
            }
            
            if ($Value instanceof \ArrayObject || $Value instanceof \ArrayIterator) {
                return self::ReplaceTypes($Value->getArrayCopy(), $TypeReplacementMap, $MaxDepth - 1, $Seen);
            }
                        
            foreach ($Reflection->getProperties() as $Property) {
                try {
                    self::ReplaceProperty($Value, $Property, $TypeReplacementMap, $MaxDepth - 1, $Seen);
                }
                catch (Exception $Exception) {}
            }
            
            return $Value;
        }
        else {
            return $Value;
        }
    }
}

?>