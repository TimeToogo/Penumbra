<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Table;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;
use \Storm\Drivers\Base\Relational\PrimaryKeys;

abstract class Persister {
    /**
     * @var int|null
     */
    private $BatchSize;
    
    public function __construct($BatchSize = null) {
        $this->BatchSize = $BatchSize;
    }

    
    final public function PersistRows(
            IConnection $Connection, 
            Table $Table, 
            array $RowsToPersist) {
        
        $KeyedRows = array();
        $UnkeyedRows = array();
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
        
        
        if($this->BatchSize === null || $this->BatchSize >= count($RowsToPersist)) {
            $this->SaveRows(
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
            foreach(array_chunk($RowsToPersist, $this->BatchSize, true) as $RowBatch) {
                $BatchedKeyedRows = array_intersect_key($RowBatch, $UnkeyedRows);
                $this->SaveRows(
                        $Connection, 
                        $Table, 
                        $BatchedKeyedRows, 
                        array_intersect_key($RowBatch, $KeyedRows), 
                        $ReturningDataKeyGenerator,
                        $PostIndividualInsertKeyGenerator);
                
                if($KeyGeneratorType === PrimaryKeys\KeyGeneratorType::PostMultiInsert) {
                    /* @var $KeyGenerator PrimaryKeys\PostMultiInsertKeyGenerator */

                    $KeyGenerator->FillPrimaryKeys($Connection, $BatchedKeyedRows);
                }
            }
        }
        
    }
    
    protected abstract function SaveRows(IConnection $Connection, Table $Table, 
            array $RowsWithoutPrimaryKey, array $RowsWithPrimaryKeys,
            PrimaryKeys\ReturningDataKeyGenerator $ReturningDataKeyGenerator = null,
            PrimaryKeys\PostIndividualInsertKeyGenerator $PostIndividualInsertKeyGenerator = null);
}

?>