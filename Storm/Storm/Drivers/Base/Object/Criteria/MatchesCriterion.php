<?php

namespace Storm\Drivers\Base\Object;

use \Storm\Core\Object;
use \Storm\Drivers\Base\Object\Criterion;

class MatchesCriterion extends Criterion {
    public function __construct(Object\PropertyData $PropertyData) {
        parent::__construct();
        
        foreach($PropertyData as $PropertyIdentifier => $Value) {
            $Property = $PropertyData->GetProperty($PropertyIdentifier);
            $this->AddPredicate(
                    Expression::BinaryOperation(
                            Expression::Property($Property), 
                            Operators\Binary::Identity, 
                            Expression::Constant($Value)));
        }        
    }
}

?>