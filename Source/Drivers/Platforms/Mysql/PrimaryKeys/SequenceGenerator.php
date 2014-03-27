<?php

namespace Penumbra\Drivers\Platforms\Mysql\PrimaryKeys;

use \Penumbra\Drivers\Base\Relational;
use \Penumbra\Drivers\Base\Relational\PrimaryKeys;
use \Penumbra\Drivers\Base\Relational\Queries\IConnection;

/**
 * Should be safe as MyISAM uses table level locking for writes.
 * This should mean that the inserted increments will be sequential.
 */
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
            throw new Relational\PlatformException(
                    '%s only supports single column: $d given',
                    get_class($this),
                    count($PrimaryKeyColumns));
        }
    }
    public function FillPrimaryKeys(IConnection $Connection, array $UnkeyedRows) {
        $PriamryKeyColumns = $this->GetPrimaryKeyColumns();
        $PrimaryKeyColumn = reset($PriamryKeyColumns);
        
        $QueryBuilder = $Connection->QueryBuilder();
        $QueryBuilder->AppendIdentifier('INSERT INTO # ', [$this->SequenceTable->GetName()]);
        $QueryBuilder->AppendIdentifier('(#)', [$this->SequenceTable->SequenceNameColumn->GetName()]);
        $QueryBuilder->Append(' VALUES ');
        
        $InsertRows = array_fill(0, count($UnkeyedRows), '(#)');
        $QueryBuilder->AppendValue(implode(',', $InsertRows), $this->SequenceName);
        
        $QueryBuilder->Build()->Execute();
        
        //Mysql will return the first inserted id
        $IncrementId = (int)$Connection->GetLastInsertIncrement();
        
        foreach($UnkeyedRows as $UnkeyedRow) {
            $UnkeyedRow[$PrimaryKeyColumn] = $PrimaryKeyColumn->ToPersistenceValue($IncrementId);
            $IncrementId++;
        }
    }
}

?>
