<?php

namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Core\Relational\Expressions\Expression as CoreExpression;
use \Storm\Drivers\Base\Relational\Columns\ObjectDataType;

abstract class ObjectMapper implements IObjectMapper {
    /**
     * @var ObjectDataType[] 
     */
    private $ObjectDataTypes;
    
    public function __construct() {
        $this->ObjectDataTypes = $this->ObjectDataTypes();
        
        foreach($this->ObjectDataTypes as $Key => $ObjectDataType) {
            $this->ObjectDataTypes[$ObjectDataType->GetClassType()] = $ObjectDataType;
            unset($this->ObjectDataTypes[$Key]);
        }
    }
    
    /**
     * @return ObjectDataType[]
     */
    protected function ObjectDataTypes() {
        return [];
    }


    final public function MapObjectExpression($Type, $Value) {
        if(isset($this->ObjectDataTypes[$Type])) {
            $ObjectDataType = $this->ObjectDataTypes[$Type];
            return Expression::Constant($ObjectDataType->ToPersistedValue($Value));
        }
        
        $MethodName = str_replace('\\', '_', $Type);
        
        if(!method_exists($this, $MethodName)) {
            throw new \Storm\Core\NotSupportedException(
                    '%s does not support mapping object instance of type %s',
                    get_class($this),
                    $Type);
        }
        
        return $this->$MethodName($Value);
    }
    
    public function ObjectMappingExample(\TypeHint $Value) {
        //Blah Blah
    }
    
    final public function MapMethodCallExpression(CoreExpression $ObjectValueExpression = null, $Type, $Name, array $ArgumentValueExpressions = []) {
        if(isset($this->ObjectDataTypes[$Type])) {
            $ObjectDataType = $this->ObjectDataTypes[$Type];
            return $ObjectDataType->MapMethodCallExpression($ObjectValueExpression, $Name, $ArgumentValueExpressions);
        }
        
        $MethodName = str_replace('\\', '_', $Type) . '_' . $Name;
        
        if(!method_exists($this, $MethodName)) {
            throw new \Storm\Core\NotSupportedException(
                    '%s does not support mapping method call %s::%s',
                    get_class($this),
                    $Type,
                    $Name);
        }
        
        $IsStatic = $ObjectValueExpression === null;
        if($IsStatic) {
            return $this->$MethodName($ArgumentValueExpressions);
        }
        else {
            return $this->$MethodName($ObjectValueExpression, $ArgumentValueExpressions);
        }
    }
    
    final public function MapPropertyFetchExpression(CoreExpression $ObjectValueExpression = null, $Type, $Name) {
        if(isset($this->ObjectDataTypes[$Type])) {
            $ObjectDataType = $this->ObjectDataTypes[$Type];
            return $ObjectDataType->MapPropertyFetchExpression($ObjectValueExpression, $Name);
        }
        
        $MethodName = str_replace('\\', '_', $Type) . '_Prop_' . $Name;
        
        if(!method_exists($this, $MethodName)) {
            throw new \Storm\Core\NotSupportedException(
                    '%s does not support mapping proptety fetch %s::$%s',
                    get_class($this),
                    $Type,
                    $Name);
        }
        
        $IsStatic = $ObjectValueExpression === null;
        if($IsStatic) {
            return $this->$MethodName();
        }
        else {
            return $this->$MethodName($ObjectValueExpression);
        }
    }
    
    public function Type_MethodCallMappingExample(CoreExpression $ObjectValueExpression, array $ArgumentExpressions) {
        //Blah Blah
    }
    
    public function Type_StaticMethodCallMappingExample(array $ArgumentExpressions) {
        //Blah Blah
    }
    
    public function Type_Prop_PropertyExample(CoreExpression $ObjectValueExpression) {
        //Blah Blah
    }
    
    public function Type_Prop_StaticPropertyExample() {
        //Blah Blah
    }
}

?>