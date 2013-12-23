<?php

namespace Storm\Core\Relational\Expressions;

use \Storm\Core\Relational;

abstract class Expression {
    use \Storm\Core\Helpers\Type;
    
    /**
     * @return ColumnExpression
     */
    public static function Column(Relational\IColumn $Column) {
        return new ColumnExpression($Column);
    }
    
    /**
     * @return ConstantExpression
     */
    public static function Constant($Value) {
        return new ConstantExpression($Value);
    }
}

?>