<?php

namespace Storm\Drivers\Base\Object\Criteria;

use \Storm\Core\Object;
use \Storm\Drivers\Base\Object\Criterion;
use \Storm\Core\Object\Expressions\Expression;
use \Storm\Core\Object\Expressions\Operators;

class MatchesCriterion extends Criterion {
    public function __construct(Object\PropertyData $PropertyData) {
        parent::__construct($PropertyData->GetEntityType());
        
        foreach($PropertyData as $PropertyIdentifier => $Value) {
            $Property = $PropertyData->GetProperty($PropertyIdentifier);
            $this->AddPredicate(
                    Expression::BinaryOperation(
                            Expression::Property($Property), 
                            Operators\Binary::Identity, 
                            Expression::Value($Value)));
        }        
    }
}

?>