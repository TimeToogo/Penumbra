<?php

namespace Storm\Drivers\Base\Object\Criteria;

use \Storm\Core\Object;
use \Storm\Drivers\Base\Object\Criteria;
use \Storm\Core\Object\Expressions\Expression;
use \Storm\Core\Object\Expressions\Operators;

class MatchesPropertyDataCriteria extends Criteria {
    public function __construct(Object\PropertyData $PropertyData) {
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
    }
}

?>