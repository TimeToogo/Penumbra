<?php

namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Drivers\Base\Relational\Traits\ForeignKey;
use \Storm\Drivers\Base\Relational\Expressions\Operators\Binary;

class ForeignKeyPredicateExpression extends CompoundBooleanExpression {
    
    public function __construct(ForeignKey $ForeignKey) {
        $ReferencedColumnMap = $ForeignKey->GetReferencedColumnMap();
        
        $ConstraintExpressions = array();
        
        foreach($ReferencedColumnMap as $PrimaryColumn) {
            $ForeignColumn = $ReferencedColumnMap[$PrimaryColumn];
            
            $ConstraintExpressions[] = Expression::BinaryOperation(
                            Expression::Column($PrimaryColumn), 
                            Binary::Equality,
                            Expression::Column($ForeignColumn));
            
        }
        
        parent::__construct($ConstraintExpressions, Binary::LogicalAnd);
    }
}

?>