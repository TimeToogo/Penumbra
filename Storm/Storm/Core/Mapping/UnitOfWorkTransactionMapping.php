<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Object;
use \Storm\Core\Relational;

/**
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class UnitOfWorkTransactionMapping {    
    
    /**
     * @var Relational\Transaction
     */
    private $Transaction;
    
    /**
     * @var \SplObjectStorage
     */
    private $MappedPersistenceData;
    
    /**
     * @var \SplObjectStorage
     */
    private $MappedDiscardenceData;
    
    public function __construct(Relational\Transaction $Transaction) {
        $this->Transaction = $Transaction;
        $this->MappedPersistenceData = new \SplObjectStorage();
        $this->MappedDiscardenceData = new \SplObjectStorage();
    }
    
    /**
     * @return Relational\Transaction
     */
    final public function GetTransaction() {
        return $this->Transaction;
    }
        
    /**
     * @param IEntityRelationalMap $EntityRelationalMap
     * @param Object\PersistenceData[] $PersistenceData
     * @return Relational\ResultRow[]
     */
    public function MapPersistenceDataArray(IEntityRelationalMap $EntityRelationalMap, array $PersistenceDataArray) {
        $ResultRowArray = [];
        
        $UnmappedPersistenceDataArray = [];
        $UnmappedResultRowArray = [];
        foreach($PersistenceDataArray as $Key => $PersistenceData) {
            if(!$this->MappedPersistenceData->contains($PersistenceData)) {
                $ResultRow = $EntityRelationalMap->ResultRow();
                $this->MappedPersistenceData[$PersistenceData] = $ResultRow;
                
                $UnmappedPersistenceDataArray[$Key] = $PersistenceData;
                $UnmappedResultRowArray[$Key] = $ResultRow;
            }
            else {
                $ResultRowArray[$Key] = $this->MappedPersistenceData[$PersistenceData];
            }
        }
        $EntityRelationalMap->MapPersistenceDataToResultRows($this, $UnmappedPersistenceDataArray, $UnmappedResultRowArray);
        $ResultRowArray += $UnmappedResultRowArray;
        
        foreach($UnmappedPersistenceDataArray as $Key => $PersistenceData) {
            $ResultRow = $ResultRowArray[$Key];
            $this->Transaction->PersistAll($ResultRow->GetRows());
        }
        
        return $ResultRowArray;
    }
    
    /**
     * @param IEntityRelationalMap $EntityRelationalMap
     * @param Object\PersistenceData $PersistenceData
     * @return Relational\ResultRow
     */
    public function MapPersistenceData(IEntityRelationalMap $EntityRelationalMap, Object\PersistenceData $PersistenceData) {
        if(!$this->MappedPersistenceData->contains($PersistenceData)) {
            $ResultRow = $EntityRelationalMap->ResultRow();
            $this->MappedPersistenceData[$PersistenceData] = $ResultRow;
            
            $EntityRelationalMap->MapPersistenceDataToResultRows($this, [$PersistenceData], [$ResultRow]);
            $this->Transaction->PersistAll($ResultRow->GetRows());
        }
        
        return $this->MappedPersistenceData[$PersistenceData];
    }
    
    /**
     * @param IEntityRelationalMap $EntityRelationalMap
     * @param Object\DiscardenceData[] $DiscardenceDataArray
     * @return Relational\PrimaryKey[]
     */
    public function MapDiscardenceDataArray(IEntityRelationalMap $EntityRelationalMap, array $DiscardenceDataArray) {
        $PrimaryKeyArray = [];
        
        $UnmappedDiscardenceDataArray = [];
        $UnmappedPrimaryKeyArray = [];
        foreach($DiscardenceDataArray as $Key => $DiscardenceData) {
            if(!$this->MappedDiscardenceData->contains($DiscardenceData)) {
                $PrimaryKey = $EntityRelationalMap->GetPrimaryKeyTable()->PrimaryKey();
                $this->MappedDiscardenceData[$DiscardenceData] = $PrimaryKey;
                
                $UnmappedDiscardenceDataArray[$Key] = $DiscardenceData;
                $UnmappedPrimaryKeyArray[$Key] = $PrimaryKey;
            }
            else {
                $PrimaryKeyArray[$Key] = $this->MappedDiscardenceData[$DiscardenceData];
            }
        }
        $PrimaryKeyArray += $EntityRelationalMap->MapDiscardenceDataToPrimaryKeys($this, $UnmappedDiscardenceDataArray);
        
        foreach($UnmappedDiscardenceDataArray as $Key => $DiscardenceData) {
            $PrimaryKey = $PrimaryKeyArray[$Key];
            $this->Transaction->Discard($PrimaryKey);
        }
        
        return $PrimaryKeyArray;
    }
    
    /**
     * @param IEntityRelationalMap $EntityRelationalMap
     * @param Object\DiscardenceData $DiscardenceData
     * @return Relational\PrimaryKey
     */
    public function MapDiscardenceData(IEntityRelationalMap $EntityRelationalMap, Object\DiscardenceData $DiscardenceData) {
        if(!$this->MappedDiscardenceData->contains($DiscardenceData)) {
            $PrimaryKey = $EntityRelationalMap->GetPrimaryKeyTable()->PrimaryKey();
            $this->MappedDiscardenceData[$DiscardenceData] = $PrimaryKey;
            
            $EntityRelationalMap->MapDiscardenceDataToPrimaryKeys($this, [$DiscardenceData], [$PrimaryKey]);
            $this->Transaction->Discard($PrimaryKey);
        }
        
        return $this->MappedDiscardenceData[$DiscardenceData];
    }
}

?>