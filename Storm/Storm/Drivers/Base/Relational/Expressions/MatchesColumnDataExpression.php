<?php

namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Core\Relational\ColumnData;
use \Storm\Drivers\Base\Relational\Expressions\Operators\Binary;

class MatchesColumnDataExpression extends CompoundBooleanExpression {
    
    public function __construct(ColumnData $ColumnData) {
        
        $ConstraintExpressions = [];
        
        foreach($ColumnData as $ColumnIdentifier => $Value) {
            $Column = $ColumnData->GetColumn($ColumnIdentifier);
            
            $ConstraintExpressions[] =
                    Expression::BinaryOperation(
                            Expression::Column($Column), 
                            Binary::Equality,
                            Expression::PersistData($Column, Expression::Constant($Value)));
        }
        
        parent::__construct($ConstraintExpressions, Binary::LogicalAnd);
    }
}

?>