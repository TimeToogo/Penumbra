<?php

namespace Storm\Drivers\Platforms\Mysql\Columns\DataTypes;

use \Storm\Drivers\Base\Relational\Columns;
use \Storm\Drivers\Base\Relational\Queries\ParameterType;
use \Storm\Core\Relational\Expressions\Expression as CoreExpression;
use \Storm\Drivers\Base\Relational\Expressions\Expression;

abstract class TimestampedDataType extends Columns\DataType {
    public function __construct($DataType) {
        parent::__construct(
                $DataType, array(), null, 
                ParameterType::Integer);
    }
    
    public function GetReviveExpression(CoreExpression $Expression) {
        return Expression::FunctionCall('UNIX_TIMESTAMP', Expression::ValueList([$Expression]));
    }
    
    public function GetPersistExpression(CoreExpression $Expression) {
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