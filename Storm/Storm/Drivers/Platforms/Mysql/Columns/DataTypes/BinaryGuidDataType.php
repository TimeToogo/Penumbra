<?php

namespace Storm\Drivers\Platforms\Mysql\Columns\DataTypes;

use \Storm\Drivers\Base\Relational\Columns;
use \Storm\Drivers\Base\Relational\Queries\ParameterType;
use \Storm\Core\Relational\Expressions\Expression as CoreExpression;
use \Storm\Drivers\Base\Relational\Expressions\Expression;

class BinaryGuidDataType extends HexedBinaryDataType {
    
    public function __construct() {
        parent::__construct(16);
    }
    
    public function ToPropertyValue($BinaryGuid) {        
        return parent::ToPropertyValue(
                substr($BinaryGuid, 12, 4) . '-' .
		substr($BinaryGuid, 8, 4) . '-' .
		substr($BinaryGuid,  0, 8) . '-' .
		substr($BinaryGuid, 16, 4) . '-' .
		substr($BinaryGuid, 20));
    }
    
    public function ToPersistedValue($FormattedGuid) {
        return parent::ToPersistedValue(
                substr($FormattedGuid, 10, 8) .
                substr($FormattedGuid, 5, 4) .
                substr($FormattedGuid,  0, 4) .
                substr($FormattedGuid, 19, 4) .
                substr($FormattedGuid, 24));
    }
}

?>