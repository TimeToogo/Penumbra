<?php

namespace Penumbra\Core\Relational;

/**
 * The request represents a range of rows to load specified by the criteria.
 * This can be thought of as a SELECT 
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Delete extends Query {    
    /**
     * The table to delete the rows from
     * 
     * @var ITable[]
     */
    private $Tables = [];
        
    final public function HasTable($TableName) {
        return isset($this->Tables[$TableName]);
    }
    
    /**
     * Add a table to the delete. (The rows of the supplied table will be deleted)
     * 
     * @param ITable[] $Tables The tables to add
     * @return void
     */
    final public function AddTable(ITable $Table) {
        if(!$this->Sources->ContainsTable($Table)) {
            throw new RelationalException(
                    'Cannot add table \'%s\' to delete: it is not part of the underlying result set source',
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
}

?>