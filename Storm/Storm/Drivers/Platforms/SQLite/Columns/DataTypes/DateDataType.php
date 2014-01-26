<?php

namespace Storm\Drivers\Platforms\SQLite\Columns\DataTypes;

use \Storm\Drivers\Base\Relational\Columns;
use \Storm\Drivers\Base\Relational\Queries\ParameterType;

class DateDataType extends Columns\DataType {
    const DateFormat = 'Y-m-d';
    
    public function __construct() {
        parent::__construct('DATE');
    }
    
    protected function ClassType() {
        return 'DateTime';
    }

    protected function PersistedValue($PropertyValue) {
        return $PropertyValue->format(self::DateFormat);
    }

    protected function PropertyValue($PersistedValue) {
        return \DateTime::createFromFormat(self::DateFormat, $PersistedValue);
    }

}

?>