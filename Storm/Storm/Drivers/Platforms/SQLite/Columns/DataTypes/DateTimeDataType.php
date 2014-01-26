<?php

namespace Storm\Drivers\Platforms\SQLite\Columns\DataTypes;

use \Storm\Drivers\Base\Relational\Columns;
use \Storm\Drivers\Base\Relational\Queries\ParameterType;
use \Storm\Drivers\Base\Relational\Expressions\Expression;
use \Storm\Core\Relational\Expressions\Expression as CoreExpression;
use \Storm\Drivers\Base\Relational\Expressions\Operators;

class DateTimeDataType extends Columns\DataType {
    const DateTimeFormat = 'Y-m-d H:i:s';
    
    public function __construct() {
        parent::__construct('DATETIME');
    }
    
    protected function ClassType() {
        return 'DateTime';
    }
    
    
    protected function PersistedValue($PropertyValue) {
        return $PropertyValue->format(self::DateTimeFormat);
    }

    protected function PropertyValue($PersistedValue) {
        return \DateTime::createFromFormat(self::DateTimeFormat, $PersistedValue);
    }
    
}

?>