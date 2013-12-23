<?php

namespace Storm\Drivers\Base\Relational\Constraints;

use \Storm\Drivers\Base\Relational\Traits\ForeignKey;
use \Storm\Drivers\Base\Relational\Expressions\Expression;
use \Storm\Drivers\Base\Relational\Expressions\Operators\Binary;

class ForeignKeyPredicate extends Predicate {
    public function __construct(ForeignKey $ForeignKey) {
        $ReferencedColumnMap = $ForeignKey->GetReferencedColumnMap();
        
        $RuleGroup = new RuleGroup(array(), true);
        
        foreach($ReferencedColumnMap as $PrimaryColumn) {
            $ForeignColumn = $ReferencedColumnMap[$PrimaryColumn];
            
            $RuleGroup->AddRule(new Rule(
                    Expression::BinaryOperation(
                            Expression::Column($PrimaryColumn), 
                            Binary::Equality,
                            Expression::Column($ForeignColumn))
                    ));
        }
        
        $this->AddRules($RuleGroup);
    }
}

?>