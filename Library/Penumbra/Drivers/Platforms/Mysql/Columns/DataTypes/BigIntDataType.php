<?php

namespace Penumbra\Drivers\Platforms\Mysql\Columns\DataTypes;

use \Penumbra\Drivers\Base\Relational\Columns;
use \Penumbra\Drivers\Base\Relational\Queries\ParameterType;

class BigIntDataType extends IntDataType {
    private static $Is64BitPHP = null;
    
    public function __construct($Unsigned = false) {
        if(self::$Is64BitPHP === null) {
            self::$Is64BitPHP = (PHP_INT_SIZE === 8);
        }
        parent::__construct('BIGINT', $Unsigned);
    }
    
    public function ToPersistedValue($PropertyValue) {
        return self::$Is64BitPHP ? (int)$PropertyValue : $PropertyValue;
    }
    
    public function ToPropertyValue($PersistedValue) {
        return self::$Is64BitPHP ? (int)$PersistedValue : $PersistedValue;
    }
}

?>