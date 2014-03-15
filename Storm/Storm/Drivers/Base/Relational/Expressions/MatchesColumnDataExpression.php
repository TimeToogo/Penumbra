<?php

namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Core\Relational\ColumnData;

class MatchesColumnDataExpression extends CompoundBooleanExpression {
    
    public function __construct(ColumnData $ColumnData) {
        
        $ConstraintExpressions = [];
        
        foreach($ColumnData->GetData() as $ColumnIdentifier => $Value) {
            $Column = $ColumnData->GetColumn($ColumnIdentifier);
            
            $ConstraintExpressions[] =
                    Expression::BinaryOperation(
                            Expression::Column($Column->GetTable(), $Column), 
                            Operators\Binary::Equality,
                            $Column->GetPersistExpression(Expression::Constant($Value)));
        }
        
        parent::__construct($ConstraintExpressions, Binary::LogicalAnd);
    }
}

?>