<?php

namespace Storm\Drivers\Platforms\Mysql\Columns\DataTypes;

use \Storm\Drivers\Base\Relational\Columns;
use \Storm\Drivers\Base\Relational\Queries\ParameterType;
use \Storm\Core\Relational\Expression as CoreExpression;
use \Storm\Drivers\Base\Relational\Expressions\Expression;

abstract class TimestampedDataType extends Columns\FunctionCallDataType {
    public function __construct($DataType) {
        parent::__construct(
                'UNIX_TIMESTAMP', 'FROM_UNIXTIME',
                $DataType, [], null, 
                ParameterType::Integer);
    }
    
    public function ToPersistedValue($PropertyValue) {
        if(!($PropertyValue instanceof \DateTime)) {
            throw new Columns\DataTypeException(
                    'The supplied property values must be an instance of \\DateTime: %s given',
                    \Storm\Core\Utilities::GetTypeOrClass($PropertyValue));
        }
        
        return $PropertyValue->getTimestamp();
    }
    
    public function ToPropertyValue($PersistedValue) {
        $DateTime = new \DateTime();
        $DateTime->setTimestamp($PersistedValue);
        
        return $DateTime;
    }
}

?>