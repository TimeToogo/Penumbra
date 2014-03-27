<?php

namespace Penumbra\Core\Object\Expressions\Aggregates;

use \Penumbra\Core\Object\Expressions\Expression;
use \Penumbra\Core\Object\Expressions\ExpressionWalker;

/**
 * Expression for an aggregate.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class AggregateExpression extends Expression {
    public function Traverse(ExpressionWalker $Walker) {
        return $this;
    }
    
    // <editor-fold desc="Factory Methods">
    
    /**
     * @return CountExpression
     */
    final public static function Count(array $UniqueValueExpressions = null) {
        return new CountExpression($UniqueValueExpressions);
    }
    
    /**
     * @return AllExpression
     */
    final public static function All(Expression $ValueExpression) {
        return new AllExpression($ValueExpression);
    }
    
    /**
     * @return AnyExpression
     */
    final public static function Any(Expression $ValueExpression) {
        return new AnyExpression($ValueExpression);
    }
    
    /**
     * @return MaximumExpression
     */
    final public static function Maximum(Expression $ValueExpression) {
        return new MaximumExpression($ValueExpression);
    }
    
    /**
     * @return MinimumExpression
     */
    final public static function Minimum(Expression $ValueExpression) {
        return new MinimumExpression($ValueExpression);
    }
    
    /**
     * @return AverageExpression
     */
    final public static function Average($UniqueValuesOnly, Expression $ValueExpression) {
        return new AverageExpression($UniqueValuesOnly, $ValueExpression);
    }
    
    /**
     * @return SumExpression
     */
    final public static function Sum($UniqueValuesOnly, Expression $ValueExpression) {
        return new SumExpression($UniqueValuesOnly, $ValueExpression);
    }
    
    /**
     * @return ImplodeExpression
     */
    final public static function Implode($UniqueValuesOnly, $Delimiter, Expression $ValueExpression) {
        return new ImplodeExpression($UniqueValuesOnly, $Delimiter, $ValueExpression);
    }
    
    // </editor-fold>
}

?>