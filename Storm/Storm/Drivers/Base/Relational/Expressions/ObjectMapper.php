<?php

namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Core\Relational\Expressions\Expression;
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
            throw new \Exception('Unimplemented object mapper');
        }
        
        return $this->$MethodName($Value);
    }
    
    public function ObjectMappingExample(\TypeHint $Value) {
        //Blah Blah
    }
    
    final public function MapMethodCallExpression(Expression $ObjectValueExpression = null, $Type, $Name, array $ArgumentValueExpressions = array()) {
        if(isset($this->ObjectDataTypes[$Type])) {
            $ObjectDataType = $this->ObjectDataTypes[$Type];
            return $ObjectDataType->MapMethodCallExpression($ObjectValueExpression, $Name, $ArgumentValueExpressions);
        }
        
        $MethodName = str_replace('\\', '_', $Type) . '_' . $Name;
        
        if(!method_exists($this, $MethodName)) {
            throw new \Exception('Unimplemented method mapper');
        }
        
        $IsStatic = $ObjectValueExpression === null;
        if($IsStatic) {
            return $this->$MethodName($ArgumentValueExpressions);
        }
        else {
            return $this->$MethodName($ObjectValueExpression, $ArgumentValueExpressions);
        }
    }
    
    public function MethodCallMappingExample(Expression $ObjectValueExpression, array $ArgumentExpressions) {
        //Blah Blah
    }
    
    public function StaticMethodCallMappingExample(array $ArgumentExpressions) {
        //Blah Blah
    }
}

?>