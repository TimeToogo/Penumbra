<?php

namespace Storm\Core\Relational;

/**
 * This select represents a single value to load
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ValueSelect extends Select {
    private $Type;
    public function __construct($Type, Criterion $Criterion) {
        parent::__construct($Criterion);
        
        if(!in_array($Type, [SelectType::Count, SelectType::Exists])) {
            throw new RelationalException(
                    '%s cannot have be of type other than SelectType::Count, SelectType::Exists',
                    get_class($this));
        }
        $this->Type = $Type;
    }

    final public function GetSelectType() {
        return $this->Type;
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