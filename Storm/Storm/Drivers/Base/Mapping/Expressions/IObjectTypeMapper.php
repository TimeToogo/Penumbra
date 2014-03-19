<?php

namespace Storm\Drivers\Base\Mapping\Expressions;

use \Storm\Core\Object\Expressions as O;
use \Storm\Drivers\Base\Relational\Expressions as R;

interface IObjectTypeMapper {
    
    /**
     * @return string
     */
    public function GetClassType();
    
    /**
     * @return R\Expression
     */
    public function MapInstance($Instance);
    
    /**
     * @return R\Expression
     */
    public function MapValue(R\Expression $ValueExpression);
    
    /**
     * @return R\Expression
     */
    public function MapNew(array $MappedArgumentExpressions);
    
    /**
     * @return R\Expression
     */
    public function MapField(R\Expression $ValueExpression, O\Expression $NameExpression, &$ReturnType);
    
    /**
     * @return R\Expression
     */
    public function MapMethodCall(R\Expression $ValueExpression, O\Expression $NameExpression, array $MappedArgumentExpressions, &$ReturnType);
    
    /**
     * @return R\Expression
     */
    public function MapIndex(R\Expression $ValueExpression, O\Expression $IndexExpression, &$ReturnType);
    
    /**
     * @return R\Expression
     */
    public function MapInvocation(R\Expression $ValueExpression, array $MappedArgumentExpressions, &$ReturnType);
    
    
}

?>