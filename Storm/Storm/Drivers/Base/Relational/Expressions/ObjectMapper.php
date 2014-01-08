<?php

namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Core\Relational\Expressions\Expression;

abstract class ObjectMapper implements IObjectMapper {
    final public function MapObjectExpression($Type, $Value) {
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
        $MethodName = str_replace('\\', '_', $Type) . '_' . $Name;
        
        if(!method_exists($this, $MethodName)) {
            throw new \Exception('Unimplemented method mapper');
        }
        
        $IsStatic = $ObjectValueExpression === null;
        if($IsStatic) {
            return $this->$MethodName($ArgumentValueExpressions);
        }
        else {
            return $this->$MethodName($ObjectExpression, $ArgumentValueExpressions);
        }
    }
    
    public function MethodCallMappingExample(Expression $ObjectExpression, array $ArgumentExpressions) {
        //Blah Blah
    }
    
    public function StaticMethodCallMappingExample(array $ArgumentExpressions) {
        //Blah Blah
    }
}

?>