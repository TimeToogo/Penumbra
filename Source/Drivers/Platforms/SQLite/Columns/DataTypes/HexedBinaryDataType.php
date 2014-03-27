<?php

namespace Penumbra\Drivers\Platforms\SQLite\Columns\DataTypes;

use \Penumbra\Drivers\Base\Relational\Columns;
use \Penumbra\Drivers\Base\Relational\Queries\ParameterType;
use \Penumbra\Core\Relational\Expression as CoreExpression;
use \Penumbra\Drivers\Base\Relational\Expressions\Expression;

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