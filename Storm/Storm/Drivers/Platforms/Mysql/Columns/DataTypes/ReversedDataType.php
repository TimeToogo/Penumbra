<?php

namespace Storm\Drivers\Platforms\Mysql\Columns\DataTypes;

use \Storm\Drivers\Base\Relational\Columns;
use \Storm\Core\Relational\Expression as CoreExpression;

class ReversedDataType extends Columns\FunctionCallDataType {
    private $DataType;
    public function __construct(Columns\DataType $DataType) {
        parent::__construct(
                'REVERSE', 'REVERSE', 
                $DataType->GetDataType(), $DataType->GetParameters(), $DataType->GetExtra(), 
                $DataType->GetParameterType());
        $this->DataType = $DataType;
    }

    public function GetReviveExpression(CoreExpression $Expression) {
        return parent::GetReviveExpression($this->DataType->GetReviveExpression($Expression));
    }
    
    public function GetPersistExpression(CoreExpression $Expression) {
        return $this->DataType->GetPersistExpression(parent::GetPersistExpression($Expression));
    }

}

?>