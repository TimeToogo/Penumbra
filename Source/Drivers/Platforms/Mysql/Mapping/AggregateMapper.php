<?php

namespace Penumbra\Drivers\Platforms\Mysql\Mapping;

use \Penumbra\Drivers\Platforms\Standard\Mapping;
use \Penumbra\Core\Object\Expressions as O;
use \Penumbra\Drivers\Base\Relational\Expressions as R;

class AggregateMapper extends Mapping\AggregateMapper {
    protected function MatchingAggregateFunctions() {
        return [
            self::Average => 'AVG',
            self::Maximum => 'MAX',
            self::Minimum => 'MIN',
            self::Sum => 'SUM',
        ];
    }
    
    public function MapAll(R\Expression $MappedValueExpression) {
        return R\Expression::FunctionCall('BIT_AND', [
                R\Expression::BinaryOperation(
                        $MappedValueExpression, 
                        R\Operators\Binary::LogicalAnd, 
                        R\Expression::BoundValue(1))
        ]);
    }
    
    public function MapAny(R\Expression $MappedValueExpression) {
        return R\Expression::FunctionCall('BIT_OR', [
                R\Expression::BinaryOperation(
                        $MappedValueExpression, 
                        R\Operators\Binary::LogicalAnd, 
                        R\Expression::BoundValue(1))
        ]);
    }

    public function MapCount(array $UniqueValueExpressions = null) {
        if($UniqueValueExpressions !== null) {
            $DisinctExpressions = [$this->DistinctKeyword];
            $First = false;
            foreach($UniqueValueExpressions as $UniqueValueExpression) {
                if($First) $First = false;
                else
                    $DisinctExpressions[] = R\Expression::Literal (',');
                
                $DisinctExpressions[] = $UniqueValueExpression;
            }
            
            return R\Expression::FunctionCall('COUNT', [
                    R\Expression::Multiple($DisinctExpressions)
            ]);
        }
        
        return R\Expression::FunctionCall('COUNT', [R\Expression::Literal('*')]);
    }

    public function MapImplode($UniqueValuesOnly, $Delimiter, R\Expression $MappedValueExpression) {
        $ArgumentExpressions = [];
        if($UniqueValuesOnly) {
            $ArgumentExpressions[] = $this->DistinctKeyword;
        }
        $ArgumentExpressions[] = $MappedValueExpression;
        $ArgumentExpressions[] = R\Expression::Keyword('SEPARATOR');
        $ArgumentExpressions[] = R\Expression::EscapedValue($Delimiter);
        
        return R\Expression::FunctionCall('GROUP_CONCAT', [R\Expression::Multiple($ArgumentExpressions)]);
    }

}

?>