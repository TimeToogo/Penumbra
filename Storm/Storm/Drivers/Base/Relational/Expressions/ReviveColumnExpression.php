<?php

namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Drivers\Base\Relational\Columns\Column;
use \Storm\Core\Relational\Expressions\ColumnExpression;

class ReviveColumnExpression extends ColumnExpression {
    private $ReviveExpression;
    public function __construct(Column $Column) {
        parent::__construct($Column);
        $this->ReviveExpression = $Column instanceof Column ?
                $Column->GetDataType()->GetReviveExpression(Expression::Column($Column)) : 
                Expression::Column($Column);
    }
    
    /**
     * @return Expression
     */
    public function GetReviveExpression() {
        return $this->ReviveExpression;
    }
}

?>