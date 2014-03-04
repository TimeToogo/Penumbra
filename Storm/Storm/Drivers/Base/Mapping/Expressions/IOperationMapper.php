<?php

namespace Storm\Drivers\Base\Mapping\Expressions;

use \Storm\Core\Relational\Expression;

/**
 * Converts object expressions to the equivalent platform-specific relational expressions 
 */
interface IOperationMapper {
    
    public function MapBinary(
            Expression $MappedLeftOperandExpression,
            $Operator,
            Expression $MappedRightOperandExpression);
        
    public function MapUnary($Operator, Expression $MappedOperandExpression);
        
    public function MapCast($CastType, Expression $MappedCastValueExpression);
}

?>