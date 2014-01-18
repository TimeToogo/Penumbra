<?php

namespace Storm\Core\Relational;

/**
 * The primary key represents the row's unique identifier.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class PrimaryKey extends TableColumnData {
    public function __construct(Table $Table, array $PrimaryKeyData = array()) {
        parent::__construct($Table, $PrimaryKeyData);
    }
    
    protected function AddColumn(IColumn $Column, $Data) {
        if(!$this->GetTable()->HasPrimaryKey($Column->GetName())) {
            throw new \InvalidArgumentException('$ColumnName must be a primary key column of ' . $this->GetTable()->GetName());
        }
        
        parent::AddColumn($Column, $Data);
    }
}

?>