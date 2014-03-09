<?php

namespace Storm\Core\Relational;

/**
 * The select represents a result set to load.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ResultSetSelect extends Select {
    private $Columns = [];
    
    public function __construct(Criterion $Criterion) {
        parent::__construct($Criterion);
    }
    
    final public function GetSelectType() {
        return SelectType::ResultSet;
    }
    
    final public function HasColumn(IColumn $Column) {
        return isset($this->Columns[$Column->GetIdentifier()]);
    }
    
    /**
     * Add a column to the select.
     * 
     * @param IColumn $Column The column to add
     * @return void
     */
    final public function AddColumn(IColumn $Column) {
        if(!$this->Criterion->HasTable($Column->GetTable()->GetName())) {
            throw new RelationalException(
                    'Cannot add column \'%s\' to relational select the parent table table \'%s\' has not part of the select',
                    $Column->GetName(),
                    $Column->GetTable()->GetName());
        }
        $this->Columns[$Column->GetIdentifier()] = $Column;
    }
        
    /**
     * Add an array of columns to the select.
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
}

?>