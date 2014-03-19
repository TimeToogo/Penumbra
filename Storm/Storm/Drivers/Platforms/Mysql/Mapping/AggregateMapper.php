<?php

namespace Storm\Drivers\Platforms\Mysql\Mapping;

use \Storm\Drivers\Platforms\Standard\Mapping;
use \Storm\Core\Object\Expressions as O;
use \Storm\Drivers\Base\Relational\Expressions as R;

class AggregateMapper extends Mapping\AggregateMapper {
    protected function MatchingAggregateFunctions() {
        return [
            self::Maximum => 'AVERAGE',
            self::Maximum => 'MAX',
            self::Minimum => 'MIN',
            self::Sum => 'SUM',
        ];
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
        $ArgumentExpressions[] = R\Expression::Keyword('SEPERATOR');
        $ArgumentExpressions[] = R\Expression::EscapedValue($Delimiter);
        
        return R\Expression::FunctionCall('GROUP_CONCAT', [R\Expression::Multiple($ArgumentExpressions)]);
    }

}

?>