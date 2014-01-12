<?php

namespace Storm\Core\Relational;

class Request {
    private $Tables = array();
    private $Columns = array();
    private $Criterion;
    
    public function __construct(array $Columns, Criterion $Criterion = null) {
        $this->Criterion = $Criterion ?: new Criterion();
        $this->AddColumns($Columns);
    }
    
    final public function AddColumn(IColumn $Column) {
        $this->Columns[$Column->GetIdentifier()] = $Column;
        $this->AddTable($Column->GetTable());
    }
    
    final public function AddColumns(array $Columns) {
        array_map([$this, 'AddColumn'], $Columns);
    }
    
    /**
     * @return Table[]
     */
    final public function GetTables() {
        return $this->Tables;
    }
    
    final public function AddTable(Table $Table) {
        $this->Tables[$Table->GetName()] = $Table;
        $this->Criterion->AddTable($Table);
    }
    
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
     * @return Criterion
     */
    final public function GetCriterion() {
        return $this->Criterion;
    }
}

?>