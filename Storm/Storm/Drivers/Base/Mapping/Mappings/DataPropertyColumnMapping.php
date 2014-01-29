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
            if(isset($ColumnData[$this->Column])) {
                $PropertyDataArray[$Key][$this->DataProperty] = $this->Column->ToPropertyValue($ColumnData[$this->Column]);
            }
            else {
                $PropertyDataArray[$Key][$this->DataProperty] = null;
            }
        }
    }
    
    public function Persist(array $PropertyDataArray, array $ColumnDataArray) {
        foreach($PropertyDataArray as $Key => $PropertyData) {
            if(isset($PropertyData[$this->DataProperty])) {
                $ColumnDataArray[$Key][$this->Column] = $this->Column->ToPersistenceValue($PropertyData[$this->DataProperty]);
            }
            else {
                $ColumnDataArray[$Key][$this->Column] = null;
            }
        }
    }
}

?>