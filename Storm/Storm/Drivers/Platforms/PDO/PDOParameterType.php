<?php

namespace Storm\Drivers\Platforms\PDO;

use \Storm\Drivers\Base\Relational\Queries\ParameterType;

final class PDOParameterType {
    private function __construct() { }
    
    public static function MapParameterType($ParameterType) {
        $PDOParameterType;
        switch ($ParameterType) {
            case ParameterType::String:
                $PDOParameterType = \PDO::PARAM_STR;
                break;
            case ParameterType::Boolean:
                $PDOParameterType = \PDO::PARAM_BOOL;
                break;
            case ParameterType::Integer:
                $PDOParameterType = \PDO::PARAM_INT;
                break;
            case ParameterType::Null:
                $PDOParameterType = \PDO::PARAM_NULL;
                break;
            case ParameterType::Binary:
                $PDOParameterType = \PDO::PARAM_LOB;
                break;
            default:
                throw new \InvalidArgumentException('$ParameterType must be a valid ParameterType');
        }
        
        return $PDOParameterType;
    }
}

?>