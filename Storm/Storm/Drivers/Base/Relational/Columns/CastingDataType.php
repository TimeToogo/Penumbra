<?php

namespace Storm\Drivers\Base\Relational\Columns;

use \Storm\Drivers\Base\Relational\Queries\ParameterType;

class CastingDataType extends DataType {
    private $CastType;
    public function __construct($DataType, $CastType, array $Parameters = [], $Extra = null, $ParameterType = ParameterType::String){
        parent::__construct($DataType, $Parameters, $Extra, $ParameterType);
        
        $this->CastType = $CastType;
    }
    
    public function ToPersistedValue($PropertyValue) {
        settype($PropertyValue, $this->CastType);
        
        return $PropertyValue;
    }
    
    public function ToPropertyValue($PersistedValue) {
        settype($PersistedValue, $this->CastType);
        
        return $PersistedValue;
    }
}

?>