<?php

namespace Storm\Core\Relational\Expressions;

use \Storm\Core\Relational;

/**
 * The base class for relation expressions.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class Expression {
    use \Storm\Core\Helpers\Type;
    
    // <editor-fold defaultstate="collapsed" desc="Factory methods">
    
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

    // </editor-fold>
}

?>