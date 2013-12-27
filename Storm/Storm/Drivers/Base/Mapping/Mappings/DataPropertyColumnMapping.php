<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping\IDataPropertyColumnMapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

class DataPropertyColumnMapping extends PropertyMapping implements IDataPropertyColumnMapping {
    private $DataProperty;
    private $Column;
    
    public function __construct(
            Object\IDataProperty $DataProperty, 
            Relational\IColumn $Column) {
        parent::__construct($DataProperty);
        
        $this->DataProperty = $DataProperty;
        $this->Column = $Column;
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