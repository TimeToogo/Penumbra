<?php

namespace Storm\Core\Relational;

final class PrimaryKey extends TableColumnData {
    public function __construct(Table $Table, array $PrimaryKeyData = array(), $DataVerified = false) {
        parent::__construct($Table, $PrimaryKeyData, $DataVerified);
    }
    
    protected function AddColumn($ColumnName, $Data) {
        if(!$this->GetTable()->HasPrimaryKey($ColumnName))
            throw new \InvalidArgumentException('$ColumnName must be a primary key column of ' . $this->GetTable()->GetName());
        
        parent::AddColumn($ColumnName, $Data);
    }
    
    public function Hash() {
        $ColumnData = $this->GetColumnData();
        
        return md5(implode(' ', array_merge([$this->GetTable()->GetName()], array_keys($ColumnData), array_values($ColumnData))));
    }
}

?>