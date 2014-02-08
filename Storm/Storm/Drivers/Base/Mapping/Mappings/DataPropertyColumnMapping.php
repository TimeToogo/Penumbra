<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

class DataPropertyColumnMapping extends PropertyMapping implements Mapping\IDataPropertyColumnMapping {
    private $IsIdentityPrimaryKeyMapping = false;
    private $DataProperty;
    private $Column;
    
    public function __construct(
            Object\IDataProperty $DataProperty, 
            Relational\IColumn $Column) {
        parent::__construct($DataProperty);
        if($DataProperty->IsIdentity() && !$Column->IsPrimaryKey()) {
            throw new MappingException(
                    'Cannot map an identity property to a non primary key column %s.%s',
                    $Column->GetTable()->GetName(),
                    $Column->GetName());
        }
        else if($Column->IsPrimaryKey() && !$DataProperty->IsIdentity()) {
            throw new MappingException(
                    'Cannot map an non identity property to a primary key column %s.%s',
                    $Column->GetTable()->GetName(),
                    $Column->GetName());
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
        }
    }
    
    public function Persist(array $PropertyDataArray, array $ColumnDataArray) {
        foreach($PropertyDataArray as $Key => $PropertyData) {
            if(isset($PropertyData[$this->DataProperty])) {
                $ColumnDataArray[$Key][$this->Column] = $this->Column->ToPersistenceValue($PropertyData[$this->DataProperty]);
            }
        }
    }
}

?>