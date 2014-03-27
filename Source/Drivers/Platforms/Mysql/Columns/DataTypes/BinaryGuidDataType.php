<?php

namespace Penumbra\Drivers\Platforms\Mysql\Columns\DataTypes;

use \Penumbra\Drivers\Base\Relational\Columns;
use \Penumbra\Drivers\Base\Relational\Queries\ParameterType;
use \Penumbra\Core\Relational\Expression as CoreExpression;
use \Penumbra\Drivers\Base\Relational\Expressions\Expression;

//Guid reordered to prevent index fragmentation
class BinaryGuidDataType extends Columns\DataType {
    public function __construct() {
        parent::__construct(
                'BINARY', [16], null,
                ParameterType::String);
    }
    
    public function GetReviveExpression(CoreExpression $Expression) {
        $HexedGuid = Expression::FunctionCall('HEX', [$Expression]);
        $Substring = function ($Expression, $Index, $Length = null) {
            //Mysql substring index is 1 based
            $Arguments = [$Expression, Expression::BoundValue($Index + 1)];
            if($Length !== null) {
                $Arguments[] = Expression::BoundValue($Length);
            }
            return Expression::FunctionCall('SUBSTR', $Arguments);
        };
        
        return Expression::FunctionCall('CONCAT_WS', [
                '-', 
                $Substring($HexedGuid, 12, 8),
                $Substring($HexedGuid, 4, 4),
                $Substring($HexedGuid, 0, 4),
                $Substring($HexedGuid, 8, 4),
                $Substring($HexedGuid, 20)]);
    }
    
    public function ToPersistedValue($FormattedGuid) {
        return parent::ToPersistedValue(
                substr($FormattedGuid, 14, 4) .
                substr($FormattedGuid, 9, 4) .
                substr($FormattedGuid, 19, 4) .
                substr($FormattedGuid,  0, 8) .
                substr($FormattedGuid, 24));
    }
    
    public function GetPersistExpression(CoreExpression $Expression) {
        return Expression::FunctionCall('UNHEX', [$Expression]);
    }
}

?>