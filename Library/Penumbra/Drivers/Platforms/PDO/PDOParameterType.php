<?php

namespace Penumbra\Drivers\Platforms\PDO;

use \Penumbra\Drivers\Base\Relational\Queries\ParameterType;

final class PDOParameterType {
    private function __construct() { }
    
    private static $ParameterTypesMap = [
        ParameterType::String => \PDO::PARAM_STR,
        ParameterType::Integer => \PDO::PARAM_INT,
        ParameterType::Double => \PDO::PARAM_STR,
        ParameterType::Boolean => \PDO::PARAM_INT,
        ParameterType::Null => \PDO::PARAM_NULL,
        ParameterType::Stream => \PDO::PARAM_LOB,
    ];
    public static function MapParameterType($ParameterType) {
        if(isset(self::$ParameterTypesMap[$ParameterType])) {
            return self::$ParameterTypesMap[$ParameterType];
        }
        else {
            throw new \Penumbra\Core\UnexpectedValueException(
                    'Cannot map the supplied parameter type: %s given',
                    \Penumbra\Utilities\Type::GetTypeOrClass($ParameterType));
        }
    }
}

?>