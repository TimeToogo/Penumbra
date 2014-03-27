<?php

namespace Penumbra\Drivers\Platforms\Mysql\Columns\DataTypes;

use \Penumbra\Drivers\Base\Relational\Columns;
use \Penumbra\Drivers\Base\Relational\Queries\ParameterType;

class EnumDataType extends Columns\DataType {
    public function __construct(array $ValuesMap) {
        parent::__construct('ENUM', array_keys($ValuesMap));
    }
    
    
}

?>