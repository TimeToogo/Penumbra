<?php

namespace Storm\Core\Relational;

/**
 * The request represents a range of rows to load specified by the criterion.
 * This can be though of as a SELECT 
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Request {
    /**
     * The tables to load from.
     * 
     * @var Table[]
     */
    private $Tables = array();
    
    /**
     * The columns to load from.
     * 
     * @var Table[]
     */
    private $Columns = array();
    
    /**
     * @var Criterion
     */
    private $Criterion;
    
    public function __construct(array $Columns, Criterion $Criterion = null) {
        $this->Criterion = $Criterion ?: new Criterion();
        $this->AddColumns($Columns);
    }
    
    /**
     * Add a column to the request.
     * 
     * @param IColumn $Column The column to add
     * @return void
     */
    final public function AddColumn(IColumn $Column) {
        $this->Columns[$Column->GetIdentifier()] = $Column;
        $this->AddTable($Column->GetTable());
    }
        
    /**
     * Add an array of columns to the request.
     * 
     * @param IColumn[] $Column The columns to add
     * @return void
     */
    final public function AddColumns(array $Columns) {
        array_walk($Columns, [$this, 'AddColumn']);
    }
    
    /**
     * Add a table to the request.
     * 
     * @param Table $Table The table to add
     * @return void
     */
    final public function AddTable(Table $Table) {
        $this->Tables[$Table->GetName()] = $Table;
        $this->Criterion->AddTable($Table);
    }
    
    /**
     * Add an array of tables to the request.
     * 
     * @param Table[] $Tables The tables to add
     * @return void
     */
    final public function AddTables(array $Tables) {
        array_walk($Tables, [$this, 'AddTable']);
    }
    
    /**
     * @return IColumn[]
     */
    final public function GetColumns() {
        return $this->Columns;
    }
    
    /**
     * @return Table[]
     */
    final public function GetTables() {
        return $this->Tables;
    }
    
    /**
     * @return Criterion
     */
    final public function GetCriterion() {
        return $this->Criterion;
    }
}

?>