<?php

namespace Storm\Drivers\Base\Relational\Columns;

use \Storm\Drivers\Base\Relational\Queries\ParameterType;
use \Storm\Core\Relational\Expression as CoreExpression;
use \Storm\Drivers\Base\Relational\Expressions\Expression;

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