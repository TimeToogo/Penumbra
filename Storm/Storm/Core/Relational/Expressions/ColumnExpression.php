<?php

namespace Storm\Core\Relational\Expressions;

use \Storm\Core\Relational\ITable;
use Storm\Core\Relational\IColumn;

/**
 * Expression representing a column in a table.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ColumnExpression extends Expression {
    /**
     * @var ITable 
     */
    private $Table;
    
    /**
     * @var IColumn 
     */
    private $Column;
    
    /**
     * @var string|null
     */
    private $Alias;
    
    public function __construct(IColumn $Column, $Alias = null) {        
        $this->Table = $Column->GetTable();
        $this->Column = $Column;
        $this->Alias = $Alias;
    }
    
    /**
     * @return ITable
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
    
    /**
     * @return string|null
     */
    final public function GetAlias() {
        return $this->Alias;
    }
}

?>