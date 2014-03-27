<?php

namespace Penumbra\Drivers\Base\Mapping\Expressions;

use \Penumbra\Core\Object\Expressions\Aggregates as O;
use \Penumbra\Drivers\Base\Relational\Expressions as R;

interface IAggregateMapper {
    
    /**
     * @return R\Expression
     */
    public function MapAll(R\Expression $MappedValueExpression);
    
    /**
     * @return R\Expression
     */
    public function MapAny(R\Expression $MappedValueExpression);
    
    /**
     * @return R\Expression
     */
    public function MapAverage($UniqueValuesOnly, R\Expression $MappedValueExpression);
    
    /**
     * @return R\Expression
     */
    public function MapCount(array $UniqueValueExpressions = null);
    
    /**
     * @return R\Expression
     */
    public function MapImplode($UniqueValuesOnly, $Delimiter, R\Expression $MappedValueExpression);
    
    /**
     * @return R\Expression
     */
    public function MapMaximum(R\Expression $MappedValueExpression);
    
    /**
     * @return R\Expression
     */
    public function MapMinimum(R\Expression $MappedValueExpression);
    
    /**
     * @return R\Expression
     */
    public function MapSum($UniqueValuesOnly, R\Expression $MappedValueExpression);
}

?>