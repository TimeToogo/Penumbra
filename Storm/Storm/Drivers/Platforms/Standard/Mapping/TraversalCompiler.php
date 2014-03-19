<?php

namespace Storm\Drivers\Platforms\Standard\Mapping;

use \Storm\Drivers\Platforms\Base\Mapping;
use \Storm\Core\Object\Expressions as O;
use \Storm\Drivers\Base\Relational\Expressions as R;

final class TraversalCompiler {
    /**
     * @return string
     */
    public function CompileTraversalExpression(O\TraversalExpression $TraversalExpression, &$TraversalString) {
        if($TraversalExpression->GetValueExpression() instanceof O\TraversalExpression) {
            return $this->CompileTraversalExpression($TraversalExpression, $TraversalString);
        }
        
        switch (true) {
            case $TraversalExpression instanceof O\FieldExpression
                    && $TraversalExpression->GetNameExpression() instanceof O\ValueExpression:
                $TraversalString .= '->' . $TraversalExpression->GetNameExpression()->GetValue();
                break;
            
            case $TraversalExpression instanceof O\IndexExpression
                    && $TraversalExpression->GetIndexExpression() instanceof O\ValueExpression:
                $TraversalString .= '[' . var_export($TraversalExpression->GetIndexExpression()->GetValue(), true) . ']';
                break;
            
            case $TraversalExpression instanceof O\MethodCallExpression
                    && $TraversalExpression->GetNameExpression() instanceof O\ValueExpression
                    && count(array_filter($TraversalExpression->GetArgumentExpressions(), function ($I) { return !($I instanceof R\ValueExpression); })) === 0:
                $TraversalString .= '->' . $TraversalExpression->GetNameExpression()->GetValue() . 
                            '(' . 
                            implode(',', array_map(function ($I) { return var_export($I, true); }, $TraversalExpression->GetArgumentExpressions()))
                            . ')';
                break;
            
            case $TraversalExpression instanceof O\InvocationExpression
                    && count(array_filter($TraversalExpression->GetArgumentExpressions(), function ($I) { return !($I instanceof R\ValueExpression); })) === 0:
                $TraversalString .= '(' . implode(',', array_map(function ($I) { return var_export($I, true); }, $TraversalExpression->GetArgumentExpressions())) . ')';
                break;

            default:
                throw new \Storm\Core\Mapping\MappingException('Unresolvable traversal');
        }
    }
}

?>