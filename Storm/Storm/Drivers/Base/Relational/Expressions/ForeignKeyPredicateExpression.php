<?php

namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Drivers\Base\Relational\Traits\ForeignKey;
use \Storm\Drivers\Base\Relational\Expressions\Operators\Binary;

class ForeignKeyPredicateExpression extends CompoundBooleanExpression {
    
    public function __construct(ForeignKey $ForeignKey) {
        $ParentTable = $ForeignKey->GetParentTable();
        $ReferencedTable = $ForeignKey->GetReferencedTable();
        $ReferencedColumnMap = $ForeignKey->GetReferencedColumnMap();
        
        $ConstraintExpressions = [];
        
        foreach($ReferencedColumnMap as $ParentColumn) {
            $ReferencedColumn = $ReferencedColumnMap[$ParentColumn];
            
            $ConstraintExpressions[] = Expression::BinaryOperation(
                            Expression::Column($ParentTable, $ParentColumn), 
                            Binary::Equality,
                            Expression::Column($ReferencedTable, $ReferencedColumn));
            
        }
        
        parent::__construct($ConstraintExpressions, Binary::LogicalAnd);
    }
}

?>