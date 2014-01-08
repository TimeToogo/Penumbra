<?php

namespace Storm\Drivers\Platforms\Mysql;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Expressions\Expression;
use \Storm\Core\Relational\Expressions\Expression as CoreExpression;
use \Storm\Drivers\Base\Relational\Expressions as E;
use \Storm\Core\Relational\Expressions as EE;
use \Storm\Drivers\Base\Relational\Expressions\Operators as O;

final class ObjectMapper extends E\ObjectMapper {
    
    
    // <editor-fold defaultstate="collapsed" desc="Date and Time">
    
    public function DateTime(\DateTime $Value) {
        return Expression::FunctionCall('FROM_UNIXTIME', 
                Expression::ValueList([Expression::Constant($Value->getTimestamp())]));
    }
    
    public function DateInterval(\DateInterval $Value) {
        return Expression::Constant($Value);
    }
    
    public function DateTimeZone(\DateTimeZone $Value) {
        return Expression::Constant($Value);
    }    
    
    public function DateTime___construct(array $ArgumentExpressions) {
        if(count($ArgumentExpressions) === 0) {
            return Expression::FunctionCall('NOW');
        }
        else {
            throw new \Exception();
        }
    }
    
    public function DateTime_getTimestamp(CoreExpression $ObjectExpression, array $ArgumentExpressions) {
        return Expression::FunctionCall('UNIX_TIMESTAMP',
                Expression::ValueList([$ObjectExpression]));
    }
    
    public function DateTime_diff(CoreExpression $ObjectExpression, array $ArgumentExpressions) {
        $Absolute = isset($ArgumentExpressions[1]) && $ArgumentExpressions[1];
        
        $DifferenceExpression = Expression::FunctionCall('TIMESTAMPDIFF', 
                Expression::ValueList([Expression::Keyword('SECOND'), $ObjectExpression, $ArgumentExpressions[0]]));
    
        return $Absolute ? 
                Expression::FunctionCall('ABS', Expression::ValueList([$DifferenceExpression])) : $DifferenceExpression;
    }
    
    private function AddDateTimeInterval(CoreExpression &$ObjectExpression, $Value, $Unit) {
        if($Value !== 0) {
            $ObjectExpression = Expression::FunctionCall('TIMESTAMPADD', Expression::ValueList([
                    Expression::Keyword($Unit),
                    Expression::Constant($Value),
                    $ObjectExpression
                    ]));
        }
    }        
    
    public function DateTime_add(CoreExpression $ObjectExpression, array $ArgumentExpressions) {
        $DateTimeExpression = $ObjectExpression;
        //If not dateinterval constant assume argument is in seconds for DateTime::Diff compatibility
        $IntervalExpression = $ArgumentExpressions[0];
        if($IntervalExpression instanceof EE\ConstantExpression 
                && $IntervalExpression->GetValue() instanceof \DateInterval) {
            /* @var $IntervalValue \DateInterval */ 
            $IntervalValue = $IntervalExpression->GetValue();
            $Inversion = $IntervalValue->invert === 1 ? -1 : 1;

            $this->AddDateTimeInterval($DateTimeExpression, $IntervalValue->d * $Inversion, 'DAY');
            $this->AddDateTimeInterval($DateTimeExpression, $IntervalValue->m * $Inversion, 'MONTH');
            $this->AddDateTimeInterval($DateTimeExpression, $IntervalValue->y * $Inversion, 'YEAR');
            $this->AddDateTimeInterval($DateTimeExpression, $IntervalValue->h * $Inversion, 'HOUR');
            $this->AddDateTimeInterval($DateTimeExpression, $IntervalValue->m * $Inversion, 'MINUTE');
            $this->AddDateTimeInterval($DateTimeExpression, $IntervalValue->s * $Inversion, 'SECOND');
        }
        else {
            $this->AddDateTimeInterval($DateTimeExpression, $IntervalExpression, 'SECOND');
        }
        return $DateTimeExpression;
    }
    
    public function DateTime_sub(CoreExpression $ObjectExpression, array $ArgumentExpressions) {
        
        $IntervalExpression = $ArgumentExpressions[0];
        if($IntervalExpression instanceof EE\ConstantExpression 
                && $IntervalExpression->GetValue() instanceof \DateInterval) {
            $IntervalValue = $ArgumentExpressions[0]->GetValue();
            $IntervalValue->invert = $IntervalValue->invert === 1 ? 0 : 1;
            $ArgumentExpressions[0] = $this->DateInterval($IntervalValue);
        }
        else {
            $ArgumentExpressions[0] = Expression::UnaryOperation(O\Unary::Negation, $IntervalExpression);
        }
        $this->DateTime_add($ObjectExpression, $ArgumentExpressions);
    }

    // </editor-fold>
}

?>