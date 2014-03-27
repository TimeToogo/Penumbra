<?php

namespace Penumbra\Drivers\Platforms\SQLite\Columns\DataTypes;

use \Penumbra\Drivers\Base\Relational\Columns;
use \Penumbra\Drivers\Base\Relational\Queries\ParameterType;
use \Penumbra\Drivers\Base\Relational\Expressions\Expression;
use \Penumbra\Core\Relational\Expression as CoreExpression;
use \Penumbra\Drivers\Base\Relational\Expressions\Operators;

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