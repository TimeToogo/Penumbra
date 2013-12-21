<?php

namespace Storm\Drivers\Base\Mapping;

use \Storm\Core\Containers;
use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Object\IProperty;
use \Storm\Core\Relational;
use \Storm\Core\Relational\IColumn;

abstract class EntityRelationalMap extends Mapping\EntityRelationalMap {
    private $MappingConfiguration;
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * @return IMappingConfiguration
     */
    final protected function GetMappingConfiguration() {
        return $this->MappingConfiguration;
    }
    
    final protected function GetProxyGenerator() {
        return $this->MappingConfiguration->GetProxyGenerator();
    }

    protected function OnInitialize(Mapping\DomainDatabaseMap $DomainDatabaseMap) {
        $this->MappingConfiguration = $DomainDatabaseMap->GetMappingConfiguration();
    }

    final public function MapPropertyDataToColumnData(Object\PropertyData $PropertyData, Relational\ColumnData $ColumnData) {
        foreach($this->GetPropertyMappings() as $Mapping) {
            $Property = $Mapping->GetProperty();
            if($Mapping instanceof IPropertyColumnMapping
                    && isset($PropertyData[$Property])) {
                $Mapping->GetColumn()->Store($ColumnData, $PropertyData[$Property]);
            }
        }
    }
    
    public function MapColumnDataToPropertyData(Relational\ColumnData $ColumnData, Object\PropertyData $PropertyData) {
        foreach($this->GetPropertyMappings() as $Mapping) {
            if($Mapping instanceof IPropertyColumnMapping) {
                $Column = $Mapping->GetColumn();
                if(isset($ColumnData[$Column])) {
                    $PropertyData[$Mapping->GetProperty()] = $Column->Retrieve($ColumnData);
                }
            }
        }
    }
    
    final public function GetMappedColumn(IProperty $Property) {
        foreach($this->GetPropertyColumnMappings() as $PropertyMapping) {
            $OtherProperty = $PropertyMapping->GetProperty();
            if($OtherProperty->GetName() === $Property->GetName()) {
                return $PropertyMapping->GetColumn();
            }
        }
        throw new \Storm\Core\Exceptions\UnmappedPropertyException();
    }
    
    final public function GetMappedProperty(IColumn $Column) {
        foreach($this->GetPropertyColumnMappings() as $PropertyMapping) {
            $OtherColumn = $PropertyMapping->GetColumn();
            if($OtherColumn->GetName() === $Column->GetName()) {
                return $PropertyMapping->GetProperty();
            }
        }
        throw new \Storm\Core\Exceptions\UnmappedPropertyException();
    }
}

?>