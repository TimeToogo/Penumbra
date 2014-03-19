<?php

namespace Storm\Drivers\Platforms\Base\Mapping;

use \Storm\Drivers\Base\Mapping\Expressions;
use \Storm\Core\Object\Expressions as O;
use \Storm\Drivers\Base\Relational\Expressions as R;

abstract class ObjectTypeMapper implements Expressions\IObjectTypeMapper {
    protected $ClassType;
    
    public function __construct() {
        $this->ClassType = $this->GetClass();
    }
    
    final public function GetClassType() {
        return $this->ClassType;
    }
    protected abstract function GetClass();
    
    final public function MapInstance($Instance) {
        $MappedExpresion = $this->MapClassInstance($Instance);
        if($MappedExpresion === null) {
            throw new \Storm\Core\Mapping\MappingException(
                    '%s cannot map instance',
                    get_class($this));
        }
        
        return $MappedExpresion;
    }
    protected abstract function MapClassInstance($Instance);

    final public function MapNew(array $MappedArgumentExpressions) {
        $MappedExpresion = $this->MapNewClass($MappedArgumentExpressions);
        if($MappedExpresion === null) {
            throw new \Storm\Core\Mapping\MappingException(
                    '%s cannot map new expression',
                    get_class($this));
        }
        
        return $MappedExpresion;
    }
    protected abstract function MapNewClass(array $MappedArgumentExpressions);
    
    private function GetConstantValue(O\Expression $Expression) {
        if($Expression instanceof O\ValueExpression) {
            return $Expression->GetValue();
        }
        
        throw new \Storm\Core\Mapping\MappingException(
                'Unexpected expression type: value expression required %s given',
                $Expression->GetType());
    }
    
    final public function MapField(R\Expression $ValueExpression, O\Expression $NameExpression, &$ReturnType) {
        $MappedExpresion = $this->MapIndex(
                $ValueExpression, 
                $this->GetConstantValue($NameExpression), 
                $ReturnType);
        
        if($MappedExpresion === null) {
            throw new \Storm\Core\Mapping\MappingException(
                    '%s cannot map field expression for class %s',
                    get_class($this),
                    $this->GetClassType());
        }
        
        return $MappedExpresion;
    }
    protected function MapClassField(R\Expression $ValueExpression, $Name, &$ReturnType) {}
    
    final public function MapIndex(R\Expression $ValueExpression, O\Expression $IndexExpression, &$ReturnType) {
        $MappedExpresion = $this->MapIndex(
                $ValueExpression, 
                $this->GetConstantValue($IndexExpression), 
                $ReturnType);
        
        if($MappedExpresion === null) {
            throw new \Storm\Core\Mapping\MappingException(
                    '%s cannot map index expression for class %s',
                    get_class($this),
                    $this->GetClassType());
        }
        
        return $MappedExpresion;
    }
    protected function MapClassIndex(R\Expression $ValueExpression, $Index, &$ReturnType) {}
    
    final public function MapMethodCall(
            R\Expression $ValueExpression, 
            O\Expression $NameExpression, 
            array $MappedArgumentExpressions, 
            &$ReturnType) {
        $MappedExpresion = $this->MapMethodCall(
                $ValueExpression, 
                $this->GetConstantValue($NameExpression), 
                $MappedArgumentExpressions, 
                $ReturnType);
        if($MappedExpresion === null) {
            throw new \Storm\Core\Mapping\MappingException(
                    '%s cannot map method call expression for class %s',
                    get_class($this),
                    $this->GetClassType());
        }
        
        return $MappedExpresion;
    }
    protected function MapClassMethodCall(R\Expression $ValueExpression, $Name, array $MappedArgumentExpressions, &$ReturnType) {}
    
    final public function MapInvocation(R\Expression $ValueExpression, array $MappedArgumentExpressions, &$ReturnType) {
        $MappedExpresion = $this->MapInvocation($ValueExpression, $MappedArgumentExpressions, $ReturnType);
        if($MappedExpresion === null) {
            throw new \Storm\Core\Mapping\MappingException(
                    '%s cannot map invocation expression for class %s',
                    get_class($this),
                    $this->GetClassType());
        }
        
        return $MappedExpresion;
    }
    protected function MapClassInvocation(R\Expression $ValueExpression, array $MappedArgumentExpressions, &$ReturnType) {}
}

?>