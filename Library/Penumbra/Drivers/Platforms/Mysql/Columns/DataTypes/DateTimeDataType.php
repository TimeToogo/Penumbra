<?php

namespace Penumbra\Drivers\Platforms\Mysql\Columns\DataTypes;

use \Penumbra\Drivers\Base\Relational\Columns;
use \Penumbra\Drivers\Platforms\Mysql\Mapping\Types\DateTimeMapper;

class DateTimeDataType extends Columns\ObjectDataType {
    
    public function __construct() {
        parent::__construct('DATETIME');
    }
    
    protected function ClassType() {
        return 'DateTime';
    }
    
    
    protected function PersistedValue($PropertyValue) {
        return $PropertyValue->format(DateTimeMapper::DateTimeFormat);
    }

    protected function PropertyValue($PersistedValue) {
        return \DateTime::createFromFormat(DateTimeMapper::DateTimeFormat, $PersistedValue);
    }
}

?>