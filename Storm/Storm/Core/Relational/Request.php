<?php

namespace Storm\Core\Relational;

/**
 * The request represents a range of rows to load specified by the criterion.
 * This can be thought of as a SELECT 
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
    
    public function __construct(Criterion $Criterion) {
        $this->Criterion = $Criterion;
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
        if(!$this->Criterion->HasTable($Column->GetTable()->GetName())) {
            throw new RelationalException(
                    'Cannot add column \'%s\' to relational request the parent table table \'%s\' has not part of the request',
                    $Column->GetName(),
                    $Column->GetTable()->GetName());
        }
        $this->Columns[$Column->GetIdentifier()] = $Column;
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
     * @return IColumn[]
     */
    final public function GetColumns() {
        return $this->Columns;
    }
    
    /**
     * @return ITable[]
     */
    final public function GetTables() {
        return $this->Criterion->GetAllTables();
    }
    
    /**
     * @return Criterion
     */
    final public function GetCriterion() {
        return $this->Criterion;
    }
}

?>