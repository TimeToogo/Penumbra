<?php

namespace Storm\Drivers\Base\Mapping\Expressions;

use \Storm\Drivers\Base\Relational\Expressions as R;

interface IOperationMapper {
    
    /**
     * @return R\Expression
     */
    public function MapBinary(
            R\Expression $MappedLeftOperandExpression,
            $Operator,
            R\Expression $MappedRightOperandExpression);
        
    /**
     * @return R\Expression
     */
    public function MapUnary($Operator, R\Expression $MappedOperandExpression);
    
    /**
     * @return R\Expression
     */
    public function MapCast($CastType, R\Expression $MappedCastValueExpression);
}

?>