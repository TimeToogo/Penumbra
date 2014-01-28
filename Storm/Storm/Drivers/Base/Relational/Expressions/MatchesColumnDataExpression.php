<?php

namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Core\Relational\Table;
use \Storm\Drivers\Base\Relational\Expressions\Operators\Binary;

class MatchesColumnDataExpression extends PredicateExpression {
    
    public function __construct(Table $Table, array $ColumnData) {
        
        $ConstraintExpressions = array();
        
        foreach($ColumnData as $ColumnIdentifier => $Value) {            
            $ConstraintExpressions[] =
                    Expression::BinaryOperation(
                            Expression::Column($Table->GetColumnByIdentifier($ColumnIdentifier)), 
                            Binary::Equality,
                            Expression::PersistData($Column, Expression::Constant($Value)));
        }
        
        parent::__construct($ConstraintExpressions);
    }
}

?>