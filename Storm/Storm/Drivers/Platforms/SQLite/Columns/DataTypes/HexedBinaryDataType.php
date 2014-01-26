<?php

namespace Storm\Drivers\Platforms\SQLite\Columns\DataTypes;

use \Storm\Drivers\Base\Relational\Columns;
use \Storm\Drivers\Base\Relational\Queries\ParameterType;
use \Storm\Core\Relational\Expressions\Expression as CoreExpression;
use \Storm\Drivers\Base\Relational\Expressions\Expression;

class HexedBinaryDataType extends Columns\DataType {
    public function __construct($Length) {
        parent::__construct(
                'BLOB', [$Length], null,
                ParameterType::String);
    }
    
    public function ToPersistedValue($PropertyValue) {
        return hex2bin($PropertyValue);
    }
    public function ToPropertyValue($PropertyValue) {
        return bin2hex($PropertyValue);
    }
}

?>