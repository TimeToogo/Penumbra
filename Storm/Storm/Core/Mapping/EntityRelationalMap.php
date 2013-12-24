<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Containers\Registrar;
use \Storm\Core\Object;
use \Storm\Core\Object\IProperty;
use \Storm\Core\Relational;
use \Storm\Core\Relational\IColumn;

abstract class EntityRelationalMap implements IEntityRelationalMap {
    use \Storm\Core\Helpers\Type;    
    
    private $EntityMap;
    private $PropertyMappings = array();
    private $PropertyColumnMappings = array();
    private $PropertyColumnsMap;
    private $PropertyRelationMappings = array();
    
    public function __construct() {
    }
    
    final public function Initialize(DomainDatabaseMap $DomainDatabaseMap) {
        $this->OnInitialize($DomainDatabaseMap);
        
        $this->EntityMap = $this->EntityMap($DomainDatabaseMap->GetDomain());
        if(!($this->EntityMap instanceof Object\EntityMap))
            throw new \UnexpectedValueException
                    ('Return value from ' . get_class($this) . '->EntityMap() must be a valid EntityMap');
        
        $Registrar = new Registrar(IPropertyMapping::IPropertyMappingType);
        $this->RegisterPropertyMappings($Registrar, $this->EntityMap, $DomainDatabaseMap->GetDatabase());
        
        $this->PropertyColumnsMap = new Map();
        foreach($Registrar->GetRegistered() as $PropertyMapping) {
            $this->AddPropertyMapping($PropertyMapping);
        }
        
        $this->OnInitialized($DomainDatabaseMap);
    }
    protected function OnInitialize(DomainDatabaseMap $DomainDatabaseMap) { }
    protected function OnInitialized(DomainDatabaseMap $DomainDatabaseMap) { }
    
    protected abstract function EntityMap(Object\Domain $Domain);
    protected abstract function RegisterPropertyMappings(Registrar $Registrar, Object\EntityMap $EntityMap, Relational\Database $Database);
    
    final protected function AddPropertyMapping(IPropertyMapping $PropertyMapping) {
        $ProperyName = $PropertyMapping->GetProperty()->GetName();
        if($PropertyMapping instanceof IPropertyColumnMapping) {
            $this->PropertyColumnMappings[$ProperyName] = $PropertyMapping;
            $this->PropertyColumnsMap->Map($PropertyMapping->GetProperty(), $PropertyMapping->GetColumns()); 
        }
        else if($PropertyMapping instanceof IPropertyRelationMapping) {
            $this->PropertyRelationMappings[$ProperyName] = $PropertyMapping;
        }
        else {
            throw new \UnexpectedValueException('$PropertyMapping not instance of ^');//TODO: error messages
        }
        $this->PropertyMappings[$ProperyName] = $PropertyMapping;
    }
    
    final public function GetEntityMap() {
        return $this->EntityMap;
    }
    
    final public function GetEntityType() {
        return $this->EntityMap->GetEntityType();
    }
    
    final public function GetPropertyMappings() {
        return $this->PropertyMappings;
    }
    
    final public function GetPropertyColumnMappings() {
        return $this->PropertyColumnMappings;
    }

    final public function GetProperyRelationMappings() {
        return $this->PropertyRelationMappings;
    }
    
    final public function GetMappedColumn(IProperty $Property) {
        if(isset($this->PropertyColumnsMap[$Property])) {
            return $this->PropertyColumnsMap[$Property];
        }
        else {
            throw new \Storm\Core\Exceptions\UnmappedPropertyException();
        }
    }
    
    final public function GetMappedProperty(IColumn $Column) {
        if(isset($this->PropertyColumnsMap[$Column])) {
            return $this->PropertyColumnsMap[$Column];
        }
        else {
            throw new \Storm\Core\Exceptions\UnmappedPropertyException();
        }
    }
    
    final public function GetAllMappedColumns(array $Properties = null) {
        if($Properties === null) {
            return $this->PropertyColumnsMap->GetToInstances();
        }
        return array_map([$this, 'GetMappedColumn'], $Properties);
    }
    
    final public function GetAllMappedProperties(array $Columns = null) {
        if($Columns === null) {
            return $this->PropertyColumnsMap->GetInstances();
        }
        return array_map([$this, 'GetMappedProperty'], $Columns);
    }
    

    final public function MapPropertyDataToColumnData(Object\PropertyData $PropertyData, Relational\ColumnData $ColumnData) {
        foreach($this->GetPropertyColumnMappings() as $Mapping) {
            $Property = $Mapping->GetProperty();
            if(isset($PropertyData[$Property])) {
                $Column = $Mapping->GetColumn();
                $Value =  $PropertyData[$Property];
                $Column->Store($ColumnData, $Value);
            }
        }
    }
    
    final public function MapColumnDataToPropertyData(Relational\ColumnData $ColumnData, Object\PropertyData $PropertyData) {
        foreach($this->GetPropertyColumnMappings() as $Mapping) {
            $Column = $Mapping->GetColumns();
            $Property =  $Mapping->GetProperty();
            if(isset($ColumnData[$Column])) {
               $PropertyData[$Property] = $Column->Retrieve($ColumnData);
            }
        }
    }
}

?>