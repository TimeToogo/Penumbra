<?php

namespace Penumbra\Drivers\Platforms\Mysql\Columns\DataTypes;

use \Penumbra\Drivers\Base\Relational\Columns;
use \Penumbra\Drivers\Base\Relational\Queries\ParameterType;

class DateDataType extends Columns\ObjectDataType {
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