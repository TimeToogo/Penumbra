<?php

namespace Storm\Core\Relational;

final class PrimaryKey extends TableColumnData {
    public function __construct(Table $Table, array $PrimaryKeyData = array()) {
        parent::__construct($Table, $PrimaryKeyData);
    }
    
    protected function AddColumn($ColumnName, $Data) {
        if(!$this->GetTable()->HasPrimaryKey($ColumnName)) {
            throw new \InvalidArgumentException('$ColumnName must be a primary key column of ' . $this->GetTable()->GetName());
        }
        
        parent::AddColumn($ColumnName, $Data);
    }
}

?>