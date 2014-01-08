<?php

namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Core\Relational\Expressions\Expression;

abstract class ExpressionMapper implements IExpressionMapper {
    private $FunctionMapper;
    private $ObjectMapper;
    
    public function __construct(IFunctionMapper $FunctionMapper, IObjectMapper $ObjectMapper) {
        $this->FunctionMapper = $FunctionMapper;
        $this->ObjectMapper = $ObjectMapper;
    }
    
    public function MapConstantExpression($Value) {
        return Expression::Constant($Value);
    }
    
    final public function MapObjectExpression($Type, $Value) {
        return $this->ObjectMapper->MapObjectExpression($Type, $Value);
    }
    
    final public function MapMethodCallExpression(Expression $ObjectValueExpression = null, $Type, $Name, array $ArgumentValueExpressions) {
        return $this->ObjectMapper->MapMethodCallExpression($ObjectValueExpression, $Type, $Name, $ArgumentValueExpressions);
    }
    
    final public function MapFunctionCallExpression($FunctionName, array $ArgumentValueExpression) {
        return $this->FunctionMapper->MapFunctionCallExpression($FunctionName, $ArgumentValueExpression);
    }
}

?>