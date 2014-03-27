<?php

namespace Penumbra\Drivers\Base\Relational\Columns;

use \Penumbra\Drivers\Base\Relational\Queries\ParameterType;
use \Penumbra\Core\Relational\Expression as CoreExpression;
use \Penumbra\Drivers\Base\Relational\Expressions\Expression;

class FunctionCallDataType extends DataType {
    private $ReviveFunctionName;
    private $PersistFunctionName;
    
    public function __construct(
            $ReviveFunctionName,
            $PersistFunctionName,
            $DataType, 
            array $Parameters = [], 
            $Extra = null, 
            $ParameterType = ParameterType::String) {
        parent::__construct($DataType, $Parameters, $Extra, $ParameterType);
        $this->ReviveFunctionName = $ReviveFunctionName;
        $this->PersistFunctionName = $PersistFunctionName;
    }
    
    public function GetReviveExpression(CoreExpression $Expression) {
        return Expression::FunctionCall(
                $this->ReviveFunctionName, 
                [$Expression]);
    }
    
    public function GetPersistExpression(CoreExpression $Expression) {
        return Expression::FunctionCall(
                $this->PersistFunctionName, 
                [$Expression]);
    }
}
?>