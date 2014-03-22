<?php

namespace Storm\Drivers\Platforms\Mysql\Mapping\Types;

use \Storm\Drivers\Platforms\Standard\Mapping;
use \Storm\Core\Object\Expressions as O;
use \Storm\Drivers\Base\Relational\Expressions as R;

class DateTimeMapper extends Mapping\ObjectTypeMapper {
    const DateTimeFormat = 'Y-m-d H:i:s';
    private static $UTC;
    
    public function __construct() {
        parent::__construct();
        $this->__wakeup();
    }
    
    public function __wakeup() {
        if(self::$UTC === null) {
            self::$UTC = new \DateTimeZone('UTC');
        }
    }
    
    public function GetClass() {
        return 'DateTime';
    }
    
    public function MapValue(R\Expression $ValueExpression) {
        return $ValueExpression;
    }

    protected function MapClassInstance($Instance) {
        $Instance = clone $Instance;
        $Instance->setTimezone(self::$UTC);
        return R\Expression::BoundValue($Instance->format(self::DateTimeFormat));
    }
    
    protected function ReviveClassInstance($MappedValue) {
        return \DateTime::createFromFormat(self::DateTimeFormat, $MappedValue, self::$UTC);
    }

    protected function MapNewClass(array $MappedArgumentExpressions) {
        switch (count($MappedArgumentExpressions)) {
            case 0:
                return R\Expression::FunctionCall('UTC_TIMESTAMP');
            
            case 1:
                if($MappedArgumentExpressions[0] instanceof R\ValueExpression) {
                    return $this->MapClassInstance(new DateTime($MappedArgumentExpressions[0]->GetValue()));
                }
                else {
                    return R\Expression::FunctionCall('FROM_UNIXTIME', $MappedArgumentExpressions[0]);
                }
                break;
            
            default:
                throw new \Storm\Core\Mapping\MappingException('Cannot map date time with timezone');
        }
    }
    
    protected function MapClassMethodCall(R\Expression $ValueExpression, $Name, array $MappedArgumentExpressions, &$ReturnType) {
        switch ($Name) {
                
            case 'getTimestamp':
                return R\Expression::FunctionCall('FROM_UNIXTIME', $ValueExpression);
                
            case 'diff':
                return $this->MapDiff($ValueExpression, $MappedArgumentExpressions, $ReturnType);
                
            case 'add':
                return $this->MapAdd($ValueExpression, $MappedArgumentExpressions, $ReturnType);
                
            case 'sub':
                return $this->MapSub($ValueExpression, $MappedArgumentExpressions, $ReturnType);
                
            case 'setDate':
                return $this->MapSetDate($ValueExpression, $MappedArgumentExpressions, $ReturnType);
                
            case 'setTime':
                return $this->MapSetTime($ValueExpression, $MappedArgumentExpressions, $ReturnType);
                
            default:
                break;
        }
    }
    
    public function MapDiff(R\Expression $ValueExpression, array $ArgumentExpressions, &$ReturnType) {
        $ReturnType = 'DateInterval';
        
        $DifferenceExpression = 
                R\Expression::FunctionCall('TIMESTAMPDIFF', 
                        [R\Expression::Keyword('SECOND'), $ValueExpression, $ArgumentExpressions[0]]);
        
        $HasAbsolute = isset($ArgumentExpressions[1]);
        if($HasAbsolute) {
            return R\Expression::Conditional(
                    $ArgumentExpressions[1], 
                    R\Expression::FunctionCall('ABS', [$DifferenceExpression]), 
                    $DifferenceExpression);
        }
        
        return $DifferenceExpression;
    }

    private function AddDateTimeInterval(R\Expression &$ObjectExpression, R\Expression $Value, $Unit) {
        $ObjectExpression = R\Expression::FunctionCall('TIMESTAMPADD', [
                R\Expression::Keyword($Unit),
                $Value,
                $ObjectExpression
        ]);
    }

    private function AddConstantDateTimeInterval(R\Expression &$ValueExpression, $Value, $Unit) {
        if($Value !== 0) {
            $this->AddDateTimeInterval($ValueExpression, R\Expression::BoundValue($Value), $Unit);
        }
    }

    public function MapAdd(R\Expression $ValueExpression, array $ArgumentExpressions, &$ReturnType) {
        $ReturnType = $this->ClassType;
        
        //If not dateinterval constant assume argument is in seconds for DateTime::Diff compatibility
        $IntervalExpression = $ArgumentExpressions[0];
        
        if ($IntervalExpression instanceof Mapping\ContextualObjectExpression 
                && $IntervalExpression->GetObject() instanceof \DateInterval) {
            
            /* @var $IntervalValue \DateInterval */
            $IntervalValue = $IntervalExpression->GetObject();
            $Inversion = $IntervalValue->invert === 1 ? -1 : 1;
            
            $this->AddConstantDateTimeInterval($ValueExpression, $IntervalValue->d * $Inversion, 'DAY');
            $this->AddConstantDateTimeInterval($ValueExpression, $IntervalValue->m * $Inversion, 'MONTH');
            $this->AddConstantDateTimeInterval($ValueExpression, $IntervalValue->y * $Inversion, 'YEAR');
            $this->AddConstantDateTimeInterval($ValueExpression, $IntervalValue->h * $Inversion, 'HOUR');
            $this->AddConstantDateTimeInterval($ValueExpression, $IntervalValue->i * $Inversion, 'MINUTE');
            $this->AddConstantDateTimeInterval($ValueExpression, $IntervalValue->s * $Inversion, 'SECOND');
        }         
        else {
            $this->AddDateTimeInterval($ValueExpression, $IntervalExpression, 'SECOND');
        }
        
        return $ValueExpression;
    }
    
    public function MapSub(R\Expression $ValueExpression, array $ArgumentExpressions, &$ReturnType) {
        $ReturnType = $this->ClassType;
        
        $IntervalExpression = $ArgumentExpressions[0];
        
        if ($IntervalExpression instanceof Mapping\ContextualObjectExpression 
                && $IntervalExpression->GetObject() instanceof \DateInterval) {
            
            $IntervalValue = $ArgumentExpressions[0]->GetObject();
            $IntervalValue->invert = $IntervalValue->invert === 1 ? 0 : 1;
        }         
        else {
            $ArgumentExpressions[0] = R\Expression::UnaryOperation(Operators\Unary::Negation, $IntervalExpression);
        }
        
        return $this->MapAdd($ValueExpression, $ArgumentExpressions, $ReturnType);
    }
    
    public function MapSetDate(R\Expression $ValueExpression, array $ArgumentExpressions, &$ReturnType) {
        $ReturnType = $this->ClassType;
        
        return R\Expression::FunctionCall('CONCAT', [
                R\Expression::FunctionCall('CONCAT_WS', [R\Expression::BoundValue('-'), 
                        $ArgumentExpressions[0], $ArgumentExpressions[1], $ArgumentExpressions[2]]),
                R\Expression::BoundValue(' '),
                R\Expression::FunctionCall('TIME', [$ValueExpression]),
        ]);
    }
    
    public function MapSetTime(R\Expression $ValueExpression, array $ArgumentExpressions, &$ReturnType) {
        $ReturnType = $this->ClassType;
        
        return R\Expression::FunctionCall('CONCAT', [
                R\Expression::FunctionCall('DATE', [$ValueExpression]),
                R\Expression::BoundValue(' '),
                R\Expression::FunctionCall('CONCAT_WS', [R\Expression::BoundValue(':'), 
                        $ArgumentExpressions[0], $ArgumentExpressions[1], isset($ArgumentExpressions[2]) ? $ArgumentExpressions[2] : R\Expression::BoundValue('0')]),
        ]);
    }
}

?>