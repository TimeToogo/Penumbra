<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Table;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;
use \Storm\Drivers\Base\Relational\PrimaryKeys;

abstract class RowPersister implements IRowPersister {
    /**
     * @var int|null
     */
    private $InsertBatchSize;
    /**
     * @var int|null
     */
    private $DeleteBatchSize;
    
    public function __construct($InsertBatchSize = null, $DeleteBatchSize = null) {
        $this->InsertBatchSize = $InsertBatchSize;
        $this->DeleteBatchSize = $DeleteBatchSize;
    }
    
    final public function PersistRows(
            IConnection $Connection, 
            Table $Table, 
            array $RowsToPersist) {
        
        $KeyedRows = [];
        $UnkeyedRows = [];
        array_walk($RowsToPersist, 
                function (Relational\Row $Row) use (&$KeyedRows, &$UnkeyedRows) {
                    if($Row->HasPrimaryKey()) {
                        $KeyedRows[] =& $Row;
                    }
                    else {
                        $UnkeyedRows[] =& $Row;
                    }
                });
        
        $HasKeyGenerator = $Table->HasKeyGenerator();
        $KeyGeneratorType = null;
        $ReturningDataKeyGenerator = null;
        $PostIndividualInsertKeyGenerator = null;
        
        if($HasKeyGenerator) {
            $KeyGenerator = $Table->GetKeyGenerator();
            $KeyGeneratorType = $KeyGenerator->GetKeyGeneratorType();
            
            if(count($UnkeyedRows) === 0) {
                $KeyGeneratorType = null;
            }
            else if($KeyGeneratorType === PrimaryKeys\KeyGeneratorType::PreInsert) {
                /* @var $KeyGenerator PrimaryKeys\PreInsertKeyGenerator */
                
                $KeyGenerator->FillPrimaryKeys($Connection, $UnkeyedRows);
            }
            else if($KeyGeneratorType === PrimaryKeys\KeyGeneratorType::ReturningData) {
                /* @var $KeyGenerator PrimaryKeys\ReturningDataKeyGenerator */
                
                $ReturningDataKeyGenerator = $KeyGenerator;
            }
            else if($KeyGeneratorType === PrimaryKeys\KeyGeneratorType::PostIndividualInsert) {
                /* @var $KeyGenerator PrimaryKeys\PostIndividualInsertKeyGenerator */
                $PostIndividualInsertKeyGenerator = $KeyGenerator;
            }
        }
        
        
        if($this->InsertBatchSize === null || $this->InsertBatchSize >= count($RowsToPersist)) {
            $this->PersistRowBatch(
                    $Connection, 
                    $Table, 
                    $UnkeyedRows, 
                    $KeyedRows, 
                    $ReturningDataKeyGenerator,
                    $PostIndividualInsertKeyGenerator);
            
            if($KeyGeneratorType === PrimaryKeys\KeyGeneratorType::PostMultiInsert) {
                /* @var $KeyGenerator PrimaryKeys\PostMultiInsertKeyGenerator */

                $KeyGenerator->FillPrimaryKeys($Connection, $UnkeyedRows);
            }
        }
        else {
            foreach(array_chunk($RowsToPersist, $this->InsertBatchSize, true) as $RowBatch) {
                $BatchedUnkeyedRows = array_intersect_key($RowBatch, $UnkeyedRows);
                $BatchedKeyedRows = array_intersect_key($RowBatch, $KeyedRows);
                $this->PersistRowBatch(
                        $Connection, 
                        $Table, 
                        $BatchedUnkeyedRows, 
                        $BatchedKeyedRows, 
                        $ReturningDataKeyGenerator,
                        $PostIndividualInsertKeyGenerator);
                
                if($KeyGeneratorType === PrimaryKeys\KeyGeneratorType::PostMultiInsert) {
                    /* @var $KeyGenerator PrimaryKeys\PostMultiInsertKeyGenerator */

                    $KeyGenerator->FillPrimaryKeys($Connection, $BatchedUnkeyedRows);
                }
            }
        }
        
    }
    
    protected abstract function PersistRowBatch(
            IConnection $Connection, 
            Table $Table, 
            array $RowsWithoutPrimaryKey, 
            array $RowsWithPrimaryKeys,
            PrimaryKeys\ReturningDataKeyGenerator $ReturningDataKeyGenerator = null,
            PrimaryKeys\PostIndividualInsertKeyGenerator $PostIndividualInsertKeyGenerator = null);
    
    final public function DeleteRows(
            IConnection $Connection, 
            Table $Table, array 
            $PrimaryKeys) {
        $Batches = [];
        if($this->DeleteBatchSize !== null) {
            $Batches = array_chunk($PrimaryKeys, $this->DeleteBatchSize);
        }
        else {
            $Batches[] = $PrimaryKeys;
        }
        
        foreach($Batches as $PrimaryKeyBatch) {
            $this->DeleteRowBatch($Connection, $Table, $PrimaryKeyBatch);
        }
    }
    
    protected abstract function DeleteRowBatch(
            IConnection $Connection, 
            Table $Table, 
            array $PrimaryKeys);
}

?>