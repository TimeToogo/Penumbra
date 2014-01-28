<?php

namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Core\Relational\Table;
use \Storm\Drivers\Base\Relational\Expressions\Operators\Binary;

class MatchesColumnDataExpression extends PredicateExpression {
    
    public function __construct(Table $Table, array $ColumnData) {
        
        $ConstraintExpressions = array();
        
        foreach($ColumnData as $ColumnIdentifier => $Value) {
            $Column = $Table->GetColumnByIdentifier($ColumnIdentifier);
            $ConstraintExpressions[] =
                    Expression::BinaryOperation(
                            Expression::Column($Column), 
                            Binary::Equality,
                            Expression::PersistData($Column, Expression::Constant($Value)));
        }
        
        parent::__construct($ConstraintExpressions);
    }
}

?>