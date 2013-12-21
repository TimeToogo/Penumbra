<?php

namespace Storm\Drivers\Base\Relational\Constraints;

use \Storm\Core\Relational\Constraints\Rule;
use \Storm\Core\Relational\TableColumnData;
use \Storm\Drivers\Base\Relational\Expressions\Expression;
use \Storm\Drivers\Base\Relational\Expressions\Operators\Binary;

class RuleGroup extends \Storm\Core\Relational\Constraints\RuleGroup {
    
    public static function Matches(TableColumnData $ColumnData) {
        $Rules = array();
        $Table = $ColumnData->GetTable();
        $Columns = $Table->GetColumns();
        foreach($ColumnData as $ColumnName => $Value) {
            $Rules[] = new Rule(
                    Expression::BinaryOperation(
                            Expression::Column($Columns[$ColumnName]), 
                            Binary::Equality,
                            Expression::Constant($Value)));
        }
        
        return RuleGroup::All($Rules);
    }
}

?>