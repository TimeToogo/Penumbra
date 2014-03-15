<?php

namespace Storm\Core\Relational;

/**
 * The request represents a range of rows to load specified by the criteria.
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
     * @var ResultSetSources
     */
    private $Sources;
    
    /**
     * @var Criteria
     */
    private $Criteria;
    
    public function __construct(ResultSetSources $Sources, Criteria $Criteria) {
        $this->Sources = $Sources;
        $this->Criteria = $Criteria;
    }
    
    /**
     * @return ResultSetSources
     */
    final public function GetSources() {
        return $this->Sources;
    }

    /**
     * @return Criteria
     */
    final public function GetCriteria() {
        return $this->Criteria;
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
        if(!$this->Criteria->HasTable($Table->GetName())) {
            throw new RelationalException(
                    'Cannot add table \'%s\' to delete: it is not a table in the underlying criteria',
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