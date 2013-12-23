<?php

namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Core\Relational\Expressions\Expression as CoreExpression;
use \Storm\Drivers\Base\Relational\Columns\Column;
use \Storm\Core\Relational\Expressions\ColumnExpression;

class PersistDataExpression extends Expression {
    private $PersistExpression;
    public function __construct(Column $Column, CoreExpression $ValueExpression) {
        $this->PersistExpression = $Column->GetDataType()->GetPersistExpression($ValueExpression);
    }
    
    /**
     * @return CoreExpression
     */
    public function GetPersistExpression() {
        return $this->PersistExpression;
    }
}

?>