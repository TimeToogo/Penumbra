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
    private $EntityType;
    private $PrimaryKeyTable;
    private $PropertyMappings = array();
    private $IdentityPrimaryKeyMappings = array();
    private $PropertyColumnMappings = array();
    private $PropertyColumnsMap;
    private $ColumnPropertiesMap;
    private $PropertyRelationMappings = array();
    
    public function __construct() {
    }
    
    final public function Initialize(DomainDatabaseMap $DomainDatabaseMap) {
        $this->OnInitialize($DomainDatabaseMap);
        
        $this->EntityMap = $this->EntityMap($DomainDatabaseMap->GetDomain());
        $this->EntityType = $this->EntityMap->GetEntityType();
        if(!($this->EntityMap instanceof Object\EntityMap))
            throw new \UnexpectedValueException
                    ('Return value from ' . get_class($this) . '->EntityMap() must be a valid EntityMap');
        
        $this->PrimaryKeyTable = $this->PrimaryKeyTable($DomainDatabaseMap->GetDatabase());
        if(!($this->PrimaryKeyTable instanceof Relational\Table))
            throw new \UnexpectedValueException
                    ('Return value from ' . get_class($this) . '->PrimaryKeyTable() must be a valid Table');
        
        $this->PropertyColumnsMap = new Map();
        $this->ColumnPropertiesMap = new Map();
        foreach($Registrar->GetRegistered() as $PropertyMapping) {
            $this->AddPropertyMapping($PropertyMapping);
        }
        
        $this->OnInitialized($DomainDatabaseMap);
    }
    protected function OnInitialize(DomainDatabaseMap $DomainDatabaseMap) { }
    protected function OnInitialized(DomainDatabaseMap $DomainDatabaseMap) { }
    
    protected abstract function EntityMap(Object\Domain $Domain);
    protected abstract function PrimaryKeyTable(Relational\Database $Database);
    protected abstract function RegisterPropertyMappings(Registrar $Registrar, Object\EntityMap $EntityMap, Relational\Database $Database);
    
    final protected function AddPropertyMapping(IPropertyMapping $PropertyMapping) {
        $ProperyName = $PropertyMapping->GetProperty()->GetName();
        if($PropertyMapping instanceof IPropertyColumnMapping) {
            $this->AddPropertyColumnMapping($PropertyMapping);
        }
        else if($PropertyMapping instanceof IPropertyRelationMapping) {
            $this->PropertyRelationMappings[$ProperyName] = $PropertyMapping;
        }
        else {
            throw new \UnexpectedValueException('$PropertyMapping not instance of ^');//TODO: error messages
        }
        $this->PropertyMappings[$ProperyName] = $PropertyMapping;
    }
    
    private function AddPropertyColumnMapping(IPropertyColumnMapping $PropertyColumnMapping) {
        $this->PropertyColumnMappings[] = $PropertyColumnMapping;

        $Property = $PropertyColumnMapping->GetProperty();
        $Column = $PropertyColumnMapping->GetColumn();
        
        if(!isset($this->PropertyColumnsMap->Map[$Property])) {
            $this->PropertyColumnsMap->Map[$Property] = new \ArrayObject();
        }
        $this->PropertyColumnsMap[$Property][] = $Column;
        
        if(!isset($this->ColumnPropertiesMap[$Column])) {
            $this->PropertyColumnsMap->Map[$Property] = new \ArrayObject();
        }
        $this->PropertyColumnsMap[$Column][] = $Property;
    }
    
    final public function GetEntityMap() {
        return $this->EntityMap;
    }
    
    public function GetPrimaryKeyTable() {
        return $this->PrimaryKeyTable;
    }
    
    final public function GetEntityType() {
        return $this->EntityType;
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
    
    public function GetMappedPersistColumns(IProperty $Property) {
        
    }
    
    final public function GetMappedColumns(IProperty $Property) {
        if(isset($this->PropertyColumnsMap[$Property])) {
            return $this->PropertyColumnsMap[$Property]->getArrayCopy();
        }
        else {
            throw new \Storm\Core\Exceptions\UnmappedPropertyException();
        }
    }
    
    final public function GetMappedProperties(IColumn $Column) {
        if(isset($this->ColumnPropertiesMap[$Column])) {
            return $this->ColumnPropertiesMap[$Column]->getArrayCopy();
        }
        else {
            throw new \Storm\Core\Exceptions\UnmappedPropertyException();
        }
    }
    
    final public function GetAllMappedColumns(array $Properties = null) {
        $ColumnGroups = null;
        if($Properties === null) {
            $ColumnGroups = $this->PropertyColumnsMap->GetToInstances();
        }
        else {
            $ColumnGroups = array_map([$this, 'GetMappedColumns'], $Properties);
        }
        $Columns = call_user_func_array('array_merge', $ColumnGroups);
        return $Columns;
    }
    
    final public function GetAllMappedProperties(array $Columns = null) {
        $PropertyGroups = null;
        if($Columns === null) {
            $PropertyGroups = $this->ColumnPropertiesMap->GetToInstances();
        }
        else {
            $PropertyGroups = array_map([$this, 'GetMappedProperties'], $Columns);
        }
        $Properties = call_user_func_array('array_merge', $PropertyGroups);
        return $Properties;
    }
    

    final public function MapPropertyDataToColumnData(
            Object\PropertyData $PropertyData, 
            Relational\ColumnData $ColumnData) {
        foreach($this->PropertyColumnMappings as $Mapping) {
            $Property = $Mapping->GetProperty();
            if(isset($PropertyData[$Property])) {
                $Column = $Mapping->GetColumn();
                $Value =  $PropertyData[$Property];
                $Column->Store($ColumnData, $Value);
            }
        }
    }
    
    final public function MapColumnDataToPropertyData(
            Relational\ColumnData $ColumnData, 
            Object\PropertyData $PropertyData) {
        foreach($this->GetPropertyColumnMappings() as $Mapping) {
            $Column = $Mapping->GetColumn();
            $Property =  $Mapping->GetProperty();
            if(isset($ColumnData[$Column])) {
               $PropertyData[$Property] = $Column->Retrieve($ColumnData);
            }
        }
    }
}

?>