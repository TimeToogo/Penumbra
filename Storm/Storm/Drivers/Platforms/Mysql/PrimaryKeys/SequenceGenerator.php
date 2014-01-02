<?php

namespace Storm\Drivers\Platforms\Mysql\PrimaryKeys;

use \Storm\Drivers\Base\Relational;
use \Storm\Drivers\Base\Relational\PrimaryKeys;
use \Storm\Drivers\Base\Relational\Queries\IConnection;

//Should be safe as MyISAM uses table level locking for writes
class SequenceGenerator extends PrimaryKeys\PreInsertKeyGenerator {
    private $SequenceName;
    private $SequenceTable;
    public function __construct($SequenceName, SequenceTable $SequenceTable) {
        $this->SequenceName = $SequenceName;
        $this->SequenceTable = $SequenceTable;
    }
    
    public function GetSequenceName() {
        return $this->SequenceName;
    }
        
    protected function OnSetPrimaryKeyColumns(array $PrimaryKeyColumns) {
        if(count($PrimaryKeyColumns) !== 1) {
            throw new \Exception('Only supports single column');
        }
    }
    public function FillPrimaryKeys(IConnection $Connection, array $UnkeyedRows) {
        $PrimaryKeyColumn = reset($this->GetPrimaryKeyColumns());
        
        $QueryBuilder = $Connection->QueryBuilder();
        $QueryBuilder->AppendIdentifier('INSERT INTO # ', [$this->SequenceTable->GetName()]);
        $QueryBuilder->AppendIdentifier('(#)', [$this->SequenceTable->SequenceNameColumn->GetName()]);
        $QueryBuilder->Append(' VALUES ');
        
        $InsertRows = array_fill(0, count($UnkeyedRows), '(#)');
        $QueryBuilder->AppendValue(implode(',', $InsertRows), $this->SequenceName);
        
        $QueryBuilder->Build()->Execute();
        
        //Mysql will return the first inserted id
        $IncrementValue = $Connection->FetchValue('SELECT LAST_INSERT_ID()');
        
        foreach($UnkeyedRows as $UnkeyedRow) {
            $UnkeyedRow[$PrimaryKeyColumn] = $IncrementValue;
            $IncrementValue++;
        }
    }
}

?>
