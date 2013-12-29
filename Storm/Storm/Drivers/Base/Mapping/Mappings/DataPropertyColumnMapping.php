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

    public function Revive(Map $ColumnDataPropertyDataMap) {
        $Property = $this->GetProperty();
        foreach($ColumnDataPropertyDataMap as $ColumnData) {
            $PropertyData = $ColumnDataPropertyDataMap[$ColumnData];
            $PropertyData[$Property] = $this->Column->Retrieve($ColumnData);
        }
    }

    public function Persist($DataPropertyValue, Relational\ColumnData $ColumnData) {
        $this->Column->Store($ColumnData, $DataPropertyValue);
    }
}

?>