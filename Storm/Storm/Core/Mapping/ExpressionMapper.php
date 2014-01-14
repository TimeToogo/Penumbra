<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Object\Expressions\Expression as ObjectExpression;
use Storm\Core\Relational\Expressions\Expression as RelationalExpression;

abstract class ObjectRelationalExpressionMapper extends ObjectRelationalMapper {
    
    /**
     * @return RelationalExpression[]
     */
    final public function MapExpressions(IEntityRelationalMap $EntityRelationalMap, array $Expressions) {
        return call_user_func_array('array_merge',
                array_map(
                        function ($Expression) use (&$EntityRelationalMap) {
                            return $this->MapExpression($EntityRelationalMap, $Expression);
                        }, $Expressions));
    }
    
    /**
     * @return RelationalExpression[]
     */
    public abstract function MapExpression(ObjectExpression $Expression);
}

?>