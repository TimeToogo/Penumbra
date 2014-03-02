<?php

namespace Storm\Drivers\Base\Relational\Expressions\Converters;

use \Storm\Core\Relational;
use \Storm\Core\Object\Expressions as O;
use \Storm\Core\Relational\Expression;

/**
 * Converts relational expressions to an equivalent platform-specific expressions 
 */
interface IExpressionMapper {
    public function MapConstant(O\ConstantExpression $Expression);
        
    public function MapObject(O\ObjectExpression $Expression);
        
    public function MapPropertyFetch(O\FieldExpression $Expression);
            
    public function MapMethodCall(O\MethodCallExpression $Expression);
        
    public function MapArray(O\ArrayExpression $Expression);
        
    public function MapAssignment(O\AssignmentExpression $Expression);
        
    public function MapBinaryOperation(O\BinaryOperationExpression $Expression);
        
    public function MapUnaryOperation(O\UnaryOperation $Expression);
        
    public function MapCast(O\CastExpression $Expression);
        
    public function MapFunctionCall(O\FunctionCallExpression $Expression);
    
    public function MapTernary(O\TernaryExpression $Expression);
        
    public function MapPropertyExpression(O\PropertyExpression $Expression);
}

?>