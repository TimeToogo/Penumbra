<?php

namespace Storm\Drivers\Base\Relational\Columns;

use \Storm\Drivers\Base\Relational\Queries\ParameterType;
use \Storm\Core\Relational\Expressions\Expression;

abstract class ObjectDataType extends DataType {
    
    private $ClassType;
    
    public function __construct($DataType, array $Parameters = array(), $Extra = null, $ParameterType = ParameterType::String) {
        parent::__construct($DataType, $Parameters, $Extra, $ParameterType);
        
        $this->ClassType = $this->ClassType();
    }
    protected abstract function ClassType();
    
    final public function GetClassType() {
        return $this->ClassType;
    }
    
    final public function ToPersistedValue($PropertyValue) {
        if($PropertyValue === null) {
            return null;
        }
        if(!($PropertyValue instanceof $this->ClassType)) {
            throw new \Exception('Expecting ' . $this->ClassType . ': ' . $PropertyValue . ' given');
        }
        
        return $this->PersistedValue($PropertyValue);
    }
    protected abstract function PersistedValue($PropertyValue);
    
    final public function ToPropertyValue($PersistedValue) {
        if($PersistedValue === null) {
            return null;
        }
        return $this->PropertyValue($PersistedValue);
    }
    protected abstract function PropertyValue($PersistedValue);
    
    final public function MapMethodCallExpression(Expression $ObjectValueExpression = null, $Name, array $ArgumentValueExpressions = array()) {
        $MapperMethodName = $Name;
        if(strpos($Name, '__') === 0) {
            $MapperMethodName = $this->ClassType . $Name;
        }
        if(!method_exists($this, $MapperMethodName)) {
            throw new \Exception('Unimplemented method mapper');
        }

        $IsStatic = $ObjectValueExpression === null;
        if($IsStatic) {
            return $this->$MapperMethodName($ArgumentValueExpressions);
        }
        else {
            return $this->$MapperMethodName($ObjectValueExpression, $ArgumentValueExpressions);
        }
    }
}
?>