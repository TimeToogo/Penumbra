<?php

namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Drivers\Base\Relational\Traits\ForeignKey;
use \Storm\Drivers\Base\Relational\Expressions\Operators\Binary;

class CompoundBooleanExpression extends Expression {
    private $LogicalOperator;
    private $BooleanExpressions;
    
    public function __construct(array $BooleanExpressions, $LogicalOperator = Binary::LogicalAnd) {
        if($LogicalOperator !== Binary::LogicalAnd && $LogicalOperator !== Binary::LogicalOr) {
            throw new \Storm\Core\UnexpectedValueException(
                    'The supplied operator must be the logical and/or, %s given',
                    \Storm\Core\Utilities::GetTypeOrClass($LogicalOperator));
        }
        $this->LogicalOperator = $LogicalOperator;
        $this->BooleanExpressions = $BooleanExpressions;
    }
    
    public function GetLogicalOperator() {
        return $this->LogicalOperator;
    }

    public function GetBooleanExpressions() {
        return $this->BooleanExpressions;
    }
}

?>