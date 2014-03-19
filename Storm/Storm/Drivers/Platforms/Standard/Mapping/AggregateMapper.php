<?php

namespace Storm\Drivers\Platforms\Standard\Mapping;

use \Storm\Drivers\Platforms\Base\Mapping;
use \Storm\Core\Object\Expressions as O;
use \Storm\Drivers\Base\Relational\Expressions as R;

abstract class AggregateMapper extends Mapping\AggregateMapper {
    const All = 0;
    const Any = 1;
    const Average = 2;
    const Maximum = 5;
    const Minimum = 6;
    const Sum = 7;
    
    protected $AggregateFunctions;
    protected $DistinctKeyword;
    
    public function __construct() {
        $this->AggregateFunctions = $this->MatchingAggregateFunctions();
        $this->DistinctKeyword = R\Expression::Keyword($this->DistinctKeyword());
    }
    
    protected abstract function MatchingAggregateFunctions();
    
    protected function DistinctKeyword() {
        return 'DISTINCT';
    }
    
    private function VerifyMatchingAggregate($Aggregate) {
        if(!isset($this->AggregateFunctions[$Aggregate])) {
            throw new \Storm\Core\Mapping\MappingException(
                    '%s cannot map aggregate %s',
                    get_class($this),
                    $Aggregate);
        }
        
        return $this->AggregateFunctions[$Aggregate];
    }
    
    private function MakeValueAggregateExpression($Aggregate, R\Expression $ValueExpression) {
        $AggregateName = $this->VerifyMatchingAggregate($Aggregate);
        
        return R\Expression::FunctionCall($AggregateName, [$ValueExpression]);
    }
    
    private function MakeUniqueAggregateExpression($Aggregate, $IsUniqueValuesOnly, R\Expression $ValueExpression) {
        if($IsUniqueValuesOnly) {
            $AggregateName = $this->VerifyMatchingAggregate($Aggregate);
            return R\Expression::FunctionCall($AggregateName, [
                    R\Expression::Multiple([$this->DistinctKeyword, $ValueExpression])
            ]);
        }
        
        return $this->MakeValueAggregateExpression($Aggregate, $ValueExpression);
    }
    
    public function MapAll(R\Expression $MappedValueExpression) {
        return $this->MakeValueAggregateExpression(self::All, $MappedValueExpression);
    }

    public function MapAny(R\Expression $MappedValueExpression) {
        return $this->MakeValueAggregateExpression(self::Any, $MappedValueExpression);
    }

    public function MapAverage($UniqueValuesOnly, R\Expression $MappedValueExpression) {
        return $this->MakeUniqueAggregateExpression(self::Average, $UniqueValuesOnly, $MappedValueExpression);
    }

    public function MapMaximum(R\Expression $MappedValueExpression) {
        return $this->MakeValueAggregateExpression(self::Maximum, $MappedValueExpression);
    }

    public function MapMinimum(R\Expression $MappedValueExpression) {
        return $this->MakeValueAggregateExpression(self::Minimum, $MappedValueExpression);
    }

    public function MapSum($UniqueValuesOnly, R\Expression $MappedValueExpression) {
        return $this->MakeUniqueAggregateExpression(self::Sum, $UniqueValuesOnly, $MappedValueExpression);
    }

}

?>