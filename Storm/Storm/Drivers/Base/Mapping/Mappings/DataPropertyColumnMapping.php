<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping\IDataPropertyColumnMapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

class DataPropertyColumnMapping extends PropertyMapping implements IDataPropertyColumnMapping {
    private $IsIdentityPrimaryKeyMapping = false;
    private $DataProperty;
    private $Column;
    private $PropetyIdentifier;
    private $ColumnIdentifier;
    
    public function __construct(
            Object\IDataProperty $DataProperty, 
            Relational\IColumn $Column) {
        parent::__construct($DataProperty);
        if($DataProperty->IsIdentity() && !$Column->IsPrimaryKey()) {
            throw new \InvalidArgumentException('Cannot map identity property to non primary key column');
        }
        else if($Column->IsPrimaryKey() && !$DataProperty->IsIdentity()) {
            throw new \InvalidArgumentException('Cannot map primary key to non identity column');
        }
        else if($Column->IsPrimaryKey() && $DataProperty->IsIdentity()) {
            $this->IsIdentityPrimaryKeyMapping = true;
        }
        
        $this->DataProperty = $DataProperty;
        $this->Column = $Column;
        $this->PropetyIdentifier = $DataProperty->GetIdentifier();
        $this->Column = $Column->GetIdentifier();
    }
    
    public function IsIdentityPrimaryKeyMapping() {
        return $this->IsIdentityPrimaryKeyMapping;
    }
    
    public function GetPersistColumns() {
        return [$this->Column];
    }
    
    public function GetReviveColumns() {
        return [$this->Column];
    }

    /**
     * @return Object\IDataProperty
     */
    public function GetDataProperty() {
        return $this->DataProperty;
    }
    
    public function Revive(array $ColumnDataArray, array $PropertyDataArray) {
        foreach($ColumnDataArray as $Key => $ColumnData) {
            $PropertyDataArray[$Key][$this->PropetyIdentifier] = $this->Column->ToPropertyValue($ColumnData[$this->ColumnIdentifier]);
        }
    }
    
    public function Persist(array $PropertyDataArray, array $ColumnDataArray) {
        foreach($PropertyDataArray as $Key => $PropertyData) {
            $ColumnDataArray[$Key][$this->ColumnIdentifier] = $this->Column->ToPersistenceValue($PropertyData[$this->PropetyIdentifier]);
        }
    }
}

?>