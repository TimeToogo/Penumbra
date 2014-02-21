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
     * @var ITable[]
     */
    private $Tables = [];
    
    /**
     * The columns to load from.
     * 
     * @var IColumn[]
     */
    private $Columns = [];
    
    /**
     * @var Criterion
     */
    private $Criterion;
    
    public function __construct(array $Columns, Criterion $Criterion = null) {
        $this->Criterion = $Criterion ?: new Criterion();
        $this->AddColumns($Columns);
    }
    
    final public function HasColumn(IColumn $Column) {
        return isset($this->Columns[$Column->GetIdentifier()]);
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
    
    final public function RemoveColumn(IColumn $Column) {
        unset($this->Columns[$Column->GetIdentifier()]);
    }
    
    final public function RemoveColumns(array $Columns) {
        array_walk($Columns, [$this, 'RemoveColumn']);
    }
    
    /**
     * Add a table to the request.
     * 
     * @param ITable $Table The table to add
     * @return void
     */
    final public function AddTable(ITable $Table) {
        $this->Tables[$Table->GetName()] = $Table;
        $this->Criterion->AddTable($Table);
    }
    
    /**
     * Add an array of tables to the request.
     * 
     * @param ITable[] $Tables The tables to add
     * @return void
     */
    final public function AddTables(array $Tables) {
        array_walk($Tables, [$this, 'AddTable']);
    }
    
    final public function RemoveTable(ITable $Table) {
        unset($this->Tables[$Table->GetName()]);
        array_walk($Table->GetColumns(), [$this, 'RemoveColumn']);
    }
    
    final public function RemoveTables(array $Tables) {
        array_walk($Tables, [$this, 'RemoveTable']);
    }
    
    /**
     * @return IColumn[]
     */
    final public function GetColumns() {
        return $this->Columns;
    }
    
    /**
     * @return ITable[]
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