<?php

namespace Storm\Drivers\Base\Object\Criteria;

use \Storm\Core\Object;
use \Storm\Drivers\Base\Object\Criterion;
use \Storm\Core\Object\Expressions\Expression;
use \Storm\Core\Object\Expressions\Operators;

class MatchesCriterion extends Criterion {
    public function __construct(Object\EntityMap $EntityMap, array $PropertyData) {
        parent::__construct($EntityMap->GetEntityType());
        
        foreach($PropertyData as $PropertyIdentifier => $Value) {
            $this->AddPredicate(
                    Expression::BinaryOperation(
                            Expression::Property($EntityMap->GetProperty($PropertyIdentifier)), 
                            Operators\Binary::Identity, 
                            Expression::Constant($Value)));
        }
    }
}

?>