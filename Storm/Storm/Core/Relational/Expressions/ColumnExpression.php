<?php

namespace Storm\Core\Relational\Expressions;

use \Storm\Core\Relational\Table;
use Storm\Core\Relational\IColumn;

class ColumnExpression extends Expression {
    private $Table;
    private $Column;
    public function __construct(IColumn $Column) {
        parent::__construct();
        
        $this->Table = $Column->GetTable();
        $this->Column = $Column;
    }
    
    /**
     * @return Table
     */
    public function GetTable() {
        return $this->Table;
    }
        
    /**
     * @return IColumn
     */
    public function GetColumn() {
        return $this->Column;
    }
}

?>