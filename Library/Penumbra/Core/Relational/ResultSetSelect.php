<?php

namespace Penumbra\Core\Relational;

/**
 * The select represents a result set to load.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ResultSetSelect extends Select implements IResultSetSource {
    private $Columns = [];
    
    public function __construct(ResultSetSpecification $ResultSetSpecification) {
        parent::__construct($ResultSetSpecification);
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
        if(!$this->Sources->ColumnHasSource($Column)) {
            throw new RelationalException(
                    'Cannot add column \'%s\' to select: there is no result set source for the column',
                    $Column->GetName());
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