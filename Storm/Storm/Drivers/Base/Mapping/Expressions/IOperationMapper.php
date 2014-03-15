<?php

namespace Storm\Drivers\Base\Mapping\Expressions;

use \Storm\Core\Relational\Expression;

interface IOperationMapper {
    
    public function MapAssignmentToBinary(
            Expression $AssignToExpression,
            $Operator,
            Expression $AssignmentValueExpression);
    
    public function MapBinary(
            Expression $MappedLeftOperandExpression,
            $Operator,
            Expression $MappedRightOperandExpression);
        
    public function MapUnary($Operator, Expression $MappedOperandExpression);
        
    public function MapCast($CastType, Expression $MappedCastValueExpression);
}

?>