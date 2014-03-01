<?php

namespace Storm\Drivers\Platforms\Mysql\Converters;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Expressions\Converters;
use \Storm\Drivers\Base\Relational\Expressions\Expression;
use \Storm\Core\Relational\Expression as CoreExpression;
use \Storm\Drivers\Base\Relational\Expressions as E;
use \Storm\Core\Relational\Expressions as EE;
use \Storm\Drivers\Base\Relational\Expressions\Operators as O;
use \Storm\Drivers\Base\Relational\PlatformException;
use \Storm\Drivers\Platforms\Mysql\Columns\DataTypes;

final class ObjectConverter extends Converters\ObjectConverter {
    protected function ObjectDataTypes() {
        return [
            new DataTypes\DateTimeDataType(),
        ];
    }
    
    // <editor-fold defaultstate="collapsed" desc="Date and Time">
    
    
    public function DateInterval(\DateInterval $Value) {
        return Expression::Constant($Value);
    }
    
    public function DateInterval___construct(array $ArgumentExpressions) {
        if(!($ArgumentExpressions[0] instanceof EE\ConstantExpression)) {
            throw new PlatformException(
                    'Call to DateInterval::__construct() must contain a single constant value');
        }
        
        return Expression::Constant(new \DateInterval($ArgumentExpressions[0]->GetValue()));
    }

    // </editor-fold>
}

?>