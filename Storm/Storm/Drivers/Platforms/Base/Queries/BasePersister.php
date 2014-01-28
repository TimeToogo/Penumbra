<?php

namespace Storm\Drivers\Platforms\Base\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Table;
use \Storm\Drivers\Base\Relational\Queries;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;
use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Drivers\Base\Relational\PrimaryKeys\ReturningDataKeyGenerator;
use \Storm\Drivers\Base\Relational\PrimaryKeys\PostIndividualInsertKeyGenerator;

abstract class BasePersister extends Queries\Persister {
    
    final protected function SaveRows(
            IConnection $Connection, 
            Table $Table, 
            array &$RowsWithoutPrimaryKey, 
            array &$RowsWithPrimaryKeys, 
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
                    function (array &$Row) use (&$Connection, &$PostIndividualInsertKeyGenerator) {
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
            Table $Table, 
            array &$Rows,
            callable $PostIndividualInsertCallback);
    
    /**
     * @return array|void Depending on should return key data
     */
    protected abstract function UpsertRows(
            IConnection $Connection, 
            Table $Table, 
            array &$Rows,
            $ShouldReturnKeyData);
}

?>