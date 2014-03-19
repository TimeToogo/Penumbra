<?php

namespace Storm\Drivers\Base\Object\Criteria;

use \Storm\Core\Object;
use \Storm\Drivers\Base\Object\Criteria;
use \Storm\Core\Object\Expressions\Expression;
use \Storm\Core\Object\Expressions\Operators;

class MatchesPropertyDataCriteria extends Criteria {
    public function __construct(
            $EntityType,
            Object\PropertyData $PropertyData, 
            \SplObjectStorage $OrderByExpressionsAscendingMap = null, 
            $RangeOffset = 0, 
            $RangeAmount = null) {
        
        $PredicateExpressions = [];
        foreach($PropertyData as $PropertyIdentifier => $Value) {
            $Property = $PropertyData->GetProperty($PropertyIdentifier);
            $PredicateExpressions[] =
                    Expression::BinaryOperation(
                            Expression::Property($Property), 
                            Operators\Binary::Identity, 
                            Expression::Value($Value));
        }
        
        parent::__construct(
                $PropertyData->GetEntityType(),
                $PredicateExpressions);
        parent::__construct(
                $EntityType, 
                $PredicateExpressions, 
                $OrderByExpressionsAscendingMap, 
                $RangeOffset, 
                $RangeAmount);
    }
}

?>