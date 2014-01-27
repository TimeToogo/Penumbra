<?php

namespace Storm\Drivers\Platforms\Base\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Table;
use \Storm\Drivers\Base\Relational\Queries;
use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Drivers\Base\Relational\PrimaryKeys\ReturningDataKeyGenerator;
use \Storm\Drivers\Base\Relational\PrimaryKeys\PostIndividualInsertKeyGenerator;

abstract class InsertThenUpsertPersister extends Queries\Persister {
    
    final protected function SaveRows(
            IConnection $Connection, 
            Table $Table, 
            array $RowsWithoutPrimaryKey, 
            array $RowsWithPrimaryKeys, 
            ReturningDataKeyGenerator $ReturningDataKeyGenerator = null,
            PostIndividualInsertKeyGenerator $PostIndividualInsertKeyGenerator = null) {
        
        if(count($RowsWithoutPrimaryKey) > 0) {
            if($PostIndividualInsertKeyGenerator !== null) {
                $this->InsertRowsIndividually($Connection, $Table, $RowsWithoutPrimaryKey, $PostIndividualInsertKeyGenerator);
            }
            else {
                $this->InsertRows($Connection, $Table, $RowsWithoutPrimaryKey, $ReturningDataKeyGenerator);
            }
        }
        
        if(count($RowsWithPrimaryKeys) > 0) {
            $this->UpsertRows($Connection, $Table, $RowsWithPrimaryKeys);
        }
    }
    protected abstract function InsertRows(
            IConnection $Connection, 
            Table $Table, 
            array $Rows, 
            ReturningDataKeyGenerator $ReturningDataKeyGenerator = null);
    
    protected abstract function InsertRowsIndividually(
            IConnection $Connection, 
            Table $Table, 
            array $Rows,
            PostIndividualInsertKeyGenerator $PostIndividualInsertKeyGenerator);
    
    protected abstract function UpsertRows(
            IConnection $Connection, 
            Table $Table, 
            array $Rows);
}

?>