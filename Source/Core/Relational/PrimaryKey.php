<?php

namespace Penumbra\Core\Relational;

/**
 * The primary key represents the row's unique identifier.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class PrimaryKey extends TableColumnData {
    public function __construct(ITable $Table, array $PrimaryKeyData = []) {
        parent::__construct($Table, $PrimaryKeyData);
    }
    
    protected function AddColumn(IColumn $Column, $Data) {
        if(!$this->GetTable()->HasPrimaryKey($Column)) {
             throw new InvalidColumnException(
                     'The supplied column is not valid primary key of table %s: %s.%s given',
                     $this->GetTable()->GetName(),
                     $Column->GetTable()->GetName(),
                     $Column->GetName());
        }
        
        parent::AddColumn($Column, $Data);
    }
}

?>