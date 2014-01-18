<?php

namespace Storm\Core\Relational\Expressions;

use \Storm\Core\Relational\Table;
use Storm\Core\Relational\IColumn;

/**
 * Expression representing a column in a table.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ColumnExpression extends Expression {
    /**
     * @var Table 
     */
    private $Table;
    
    /**
     * @var IColumn 
     */
    private $Column;
    
    public function __construct(IColumn $Column) {        
        $this->Table = $Column->GetTable();
        $this->Column = $Column;
    }
    
    /**
     * @return Table
     */
    final public function GetTable() {
        return $this->Table;
    }
        
    /**
     * @return IColumn
     */
    final public function GetColumn() {
        return $this->Column;
    }
}

?>