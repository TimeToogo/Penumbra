<?php

namespace Storm\Core\Relational;

/**
 * The request represents a range of rows to load specified by the criterion.
 * This can be thought of as a SELECT 
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Delete {    
    /**
     * The table to delete the rows from
     * 
     * @var ITable[]
     */
    private $Tables = [];
    
    /**
     * @var Criterion
     */
    private $Criterion;
    
    public function __construct(Criterion $Criterion) {
        $this->Criterion = $Criterion;
    }
    
    final public function HasTable($TableName) {
        return isset($this->Tables[$TableName]);
    }
    
    /**
     * Add a table to the delete.
     * 
     * @param ITable[] $Tables The tables to add
     * @return void
     */
    final public function AddTable(ITable $Table) {
        if(!$this->Criterion->HasTable($Table->GetName())) {
            throw new RelationalException(
                    'Cannot add table \'%s\' to delete: it is not a table in the underlying criterion',
                    $Table->GetName());
        }
        $this->Tables[$Table->GetName()] = $Table;
    }
        
    /**
     * Add an array of tables to the delete.
     * 
     * @param ITable[] $Tables The tables to add
     * @return void
     */
    final public function AddTables(array $Tables) {
        array_walk($Tables, [$this, 'AddTable']);
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