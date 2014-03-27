<?php

namespace Penumbra\Drivers\Platforms\Base\Queries;

use \Penumbra\Core\Relational;
use \Penumbra\Drivers\Base\Relational\Queries;
use \Penumbra\Drivers\Base\Relational\Queries\QueryBuilder;
use \Penumbra\Drivers\Base\Relational\Queries\IConnection;
use \Penumbra\Drivers\Base\Relational\PrimaryKeys\ReturningDataKeyGenerator;
use \Penumbra\Drivers\Base\Relational\PrimaryKeys\PostIndividualInsertKeyGenerator;

abstract class UpsertPersister extends Queries\RowPersister {
    
    final protected function PersistRowBatch(
            IConnection $Connection, 
            Relational\ITable $Table, 
            array $RowsWithoutPrimaryKey, 
            array $RowsWithPrimaryKeys, 
            ReturningDataKeyGenerator $ReturningDataKeyGenerator = null,
            PostIndividualInsertKeyGenerator $PostIndividualInsertKeyGenerator = null) {
        
        if(count($RowsWithPrimaryKeys) === 0 && count($RowsWithoutPrimaryKey) === 0) {
            return;
        }
        
        $RowsToUpsert = null;
        if($PostIndividualInsertKeyGenerator !== null) {
            $this->InsertRowsIndividually(
                    $Connection, 
                    $Table, 
                    $RowsWithoutPrimaryKey, 
                    function (Relational\Row $Row) use (&$Connection, &$PostIndividualInsertKeyGenerator) {
                        $PostIndividualInsertKeyGenerator->FillPrimaryKey($Connection, $Row);
                    });
            
            $RowsToUpsert = $RowsWithPrimaryKeys;
        }
        else {
            $RowsToUpsert = array_merge($RowsWithoutPrimaryKey, $RowsWithPrimaryKeys);
        }
        
        if(count($RowsToUpsert) > 0) {
            $ShouldReturnKeyData = $ReturningDataKeyGenerator !== null;
            $ReturnedKeyData = $this->UpsertRows(
                    $Connection,
                    $Table,
                    $RowsToUpsert,
                    $ShouldReturnKeyData);
            
            if($ShouldReturnKeyData) {
                $ReturningDataKeyGenerator->FillPrimaryKeys($Connection, $RowsWithoutPrimaryKey, $ReturnedKeyData);
            }
        }
    }
    
    protected abstract function InsertRowsIndividually(
            IConnection $Connection, 
            Relational\ITable $Table, 
            array $Rows,
            callable $PostIndividualInsertCallback);
    
    /**
     * @return array|void Depending on should return key data
     */
    protected abstract function UpsertRows(
            IConnection $Connection, 
            Relational\ITable $Table, 
            array $Rows,
            $ShouldReturnKeyData);
}

?>