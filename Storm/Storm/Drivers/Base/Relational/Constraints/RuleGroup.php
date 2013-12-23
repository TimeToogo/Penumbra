<?php

namespace Storm\Drivers\Base\Relational\Constraints;

use \Storm\Core\Relational\Constraints\Rule;
use \Storm\Core\Relational\ColumnData;
use \Storm\Core\Relational\ResultRow;
use \Storm\Drivers\Base\Relational\Expressions\Expression;
use \Storm\Drivers\Base\Relational\Expressions\Operators\Binary;

class RuleGroup extends \Storm\Core\Relational\Constraints\RuleGroup {
    
    public static function Matches(ColumnData $ColumnData) {
        $Rules = array();
        foreach($ColumnData as $ColumnIdentifier => $Value) {
            $Column = $ColumnData->GetColumn($ColumnIdentifier);
            $Rules[] = new Rule(
                    Expression::BinaryOperation(
                            Expression::Column($Column), 
                            Binary::Equality,
                            Expression::PersistData($Column, Expression::Constant($Value))));
        }
        
        return RuleGroup::All($Rules);
    }
}

?>