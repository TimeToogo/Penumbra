<?php

namespace Storm\Drivers\Platforms\Mysql\Columns\DataTypes;

use \Storm\Drivers\Base\Relational\Columns;
use \Storm\Drivers\Base\Relational\Queries\ParameterType;

abstract class TimestampedDataType extends Columns\DataType {
    public function __construct($DataType) {
        parent::__construct(
                $DataType, array(), null, 
                ParameterType::Integer);
    }
    
    public function GetReviveExpression(Expression $Expression) {
        return Expression::FunctionCall('UNIX_TIMESTAMP', Expression::ValueList([$Expression]));
    }
    
    public function GetPersistExpression(Expression $Expression) {
        return Expression::FunctionCall('FROM_UNIXTIME', Expression::ValueList([$Expression]));
    }
    
    public function ToPersistedValue($PropertyValue) {
        if(!($PropertyValue instanceof \DateTime))
            throw new \InvalidArgumentException('$PropertyValue must be an instance of DateTime');
        
        return $PropertyValue->getTimestamp();
    }
    
    public function ToPropertyValue($PersistedValue) {
        $DateTime = new \DateTime();
        $DateTime->setTimestamp($PersistedValue);
        
        return $DateTime;
    }
}

?>