<?php

namespace Storm\Core\Relational;

use \Storm\Core\Relational\Expressions\Expression;

class Procedure extends Request {
    private $Expressions;
    
    public function __construct(array $Tables, $IsSingleRow) {
        $Columns = array();
        foreach($Tables as $Table) {
            $Columns = array_merge($Table->GetColumns(), $Columns);
        }
        parent::__construct($Columns, $IsSingleRow);
    }
    
    /**
     * @return Expression[]
     */
    final public function GetExpressions() {
        return $this->Expressions;
    }
    final public function AddExpression(Expression $Expression) {
        $this->Expressions[] = $Expression;
    }
}

?>