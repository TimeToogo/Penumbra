<?php

namespace Storm\Drivers\Platforms\Mysql\Columns\DataTypes;

use \Storm\Drivers\Base\Relational\Columns;
use \Storm\Drivers\Base\Relational\Queries\ParameterType;
use \Storm\Core\Relational\Expressions as E;
use \Storm\Drivers\Base\Relational\Expressions\Expression;
use \Storm\Core\Relational\Expressions\Expression as CoreExpression;
use \Storm\Drivers\Base\Relational\Expressions\Operators;

class DateTimeDataType extends Columns\ObjectDataType {
    const DateTimeFormat = 'Y-m-d H:i:s';
    
    public function __construct() {
        parent::__construct('DATETIME');
    }
    
    protected function ClassType() {
        return 'DateTime';
    }
    
    
    protected function PersistedValue($PropertyValue) {
        return $PropertyValue->format(self::DateTimeFormat);
    }

    protected function PropertyValue($PersistedValue) {
        return \DateTime::createFromFormat(self::DateTimeFormat, $PersistedValue);
    }
    
    // <editor-fold defaultstate="collapsed" desc="Object mapping">
    
    public function DateTime__construct(array $ArgumentExpressions) {
        if (count($ArgumentExpressions) === 0) {
            return Expression::FunctionCall('NOW');
        }         else {
            throw new \Exception();
        }
    }

    public function getTimestamp(CoreExpression $ObjectExpression, array $ArgumentExpressions) {
        return Expression::FunctionCall('UNIX_TIMESTAMP', Expression::ValueList([$ObjectExpression]));
    }

    public function diff(CoreExpression $ObjectExpression, array $ArgumentExpressions) {
        $Absolute = isset($ArgumentExpressions[1]) && $ArgumentExpressions[1]->GetValue();
        
        $DifferenceExpression = Expression::FunctionCall('TIMESTAMPDIFF', 
                Expression::ValueList([Expression::Keyword('SECOND'), $ObjectExpression, $ArgumentExpressions[0]]));
        
        return $Absolute ?
                Expression::FunctionCall('ABS', Expression::ValueList([$DifferenceExpression])) : $DifferenceExpression;
    }

    private function AddDateTimeInterval(CoreExpression &$ObjectExpression, CoreExpression $Value, $Unit) {
        $ObjectExpression = Expression::FunctionCall('TIMESTAMPADD', Expression::ValueList([
                Expression::Keyword($Unit),
                $Value,
                $ObjectExpression
        ]));
    }

    private function AddConstantDateTimeInterval(CoreExpression &$ObjectExpression, $Value, $Unit) {
        $this->AddDateTimeInterval($ObjectExpression, Expression::Constant($Value), $Unit);
    }

    public function add(CoreExpression $ObjectExpression, array $ArgumentExpressions) {
        $DateTimeExpression = $ObjectExpression;
        //If not dateinterval constant assume argument is in seconds for DateTime::Diff compatibility
        $IntervalExpression = $ArgumentExpressions[0];
        
        if ($IntervalExpression instanceof E\ConstantExpression && $IntervalExpression->GetValue() instanceof \DateInterval) {
            /* @var $IntervalValue \DateInterval */
            $IntervalValue = $IntervalExpression->GetValue();
            $Inversion = $IntervalValue->invert === 1 ? -1 : 1;
            
            if($IntervalValue->d !== 0)
                $this->AddConstantDateTimeInterval($DateTimeExpression, $IntervalValue->d * $Inversion, 'DAY');
            if($IntervalValue->m !== 0)
                $this->AddConstantDateTimeInterval($DateTimeExpression, $IntervalValue->m * $Inversion, 'MONTH');
            if($IntervalValue->y !== 0)
                $this->AddConstantDateTimeInterval($DateTimeExpression, $IntervalValue->y * $Inversion, 'YEAR');
            if($IntervalValue->h !== 0)
                $this->AddConstantDateTimeInterval($DateTimeExpression, $IntervalValue->h * $Inversion, 'HOUR');
            if($IntervalValue->i !== 0)
                $this->AddConstantDateTimeInterval($DateTimeExpression, $IntervalValue->i * $Inversion, 'MINUTE');
            if($IntervalValue->s !== 0)
                $this->AddConstantDateTimeInterval($DateTimeExpression, $IntervalValue->s * $Inversion, 'SECOND');
        }         
        else {
            $this->AddDateTimeInterval($DateTimeExpression, $IntervalExpression, 'SECOND');
        }
        return $DateTimeExpression;
    }


    public function sub(CoreExpression $ObjectExpression, array $ArgumentExpressions) {
        $IntervalExpression = $ArgumentExpressions[0];
        if ($IntervalExpression instanceof E\ConstantExpression && $IntervalExpression->GetValue() instanceof \DateInterval) {
            
            $IntervalValue = $ArgumentExpressions[0]->GetValue();
            $IntervalValue->invert = $IntervalValue->invert === 1 ? 0 : 1;
            
            $ArgumentExpressions[0] = $IntervalValue;
        }         
        else {
            $ArgumentExpressions[0] = Expression::UnaryOperation(Operators\Unary::Negation, $IntervalExpression);
        }
        
        return $this->add($ObjectExpression, $ArgumentExpressions);
    }

    // </editor-fold>
}

?>