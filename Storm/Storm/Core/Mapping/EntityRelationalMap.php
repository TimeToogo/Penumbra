<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Relational\Database;
use \Storm\Core\Object\Domain;
use \Storm\Core\Containers\Registrar;
use \Storm\Core\Object;
use \Storm\Core\Object\IProperty;
use \Storm\Core\Object\Identity;
use \Storm\Core\Relational;
use \Storm\Core\Relational\IColumn;
use \Storm\Core\Relational\PrimaryKey;

abstract class EntityRelationalMap implements IEntityRelationalMap {
    use \Storm\Core\Helpers\Type;    
    
    private $EntityMap;
    private $PropertyMappings = array();
    private $PropertyColumnMappings = array();
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
        $this->RegisterPropertyMappings($Registrar, $this->EntityMap, $this->Table);
        
        foreach($Registrar->GetRegistered() as $PropertyMapping) {
            $this->AddPropertyMapping($PropertyMapping);
        }
        
        $this->OnInitialized($DomainDatabaseMap);
    }
    protected function OnInitialize(DomainDatabaseMap $DomainDatabaseMap) { }
    protected function OnInitialized(DomainDatabaseMap $DomainDatabaseMap) { }
    
    protected abstract function EntityMap(Object\Domain $Domain);
    protected abstract function RegisterPropertyMappings(Registrar $Registrar, Object\EntityMap $EntityMap, Relational\Table $Table);
    
    final protected function AddPropertyMapping(IPropertyMapping $PropertyMapping) {
        $ProperyName = $PropertyMapping->GetProperty()->GetName();
        if($PropertyMapping instanceof IPropertyColumnMapping) {
            $this->PropertyColumnMappings[$ProperyName] = $PropertyMapping;
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
    
    public abstract function MapPropertyDataToColumnData(Object\PropertyData $PropertyData, Relational\ColumnData $ColumnData);

    public abstract function MapColumnDataToPropertyData(Relational\ColumnData $ColumnData, Object\PropertyData $PropertyData);

    /**
     * @return IColumn
     * @throws \Storm\Core\Exceptions\UnmappedPropertyException
     */
    public abstract function GetMappedColumn(IProperty $Property);
    /**
     * @return IProperty
     * @throws \Storm\Core\Exceptions\UnmappedPropertyException
     */
    public abstract function GetMappedProperty(IColumn $Column);
    
    final public function GetAllMappedColumns(array $Properties) {
        return array_map([$this, 'GetMappedColumn'], $Properties);
    }
    
    final public function GetAllMappedProperties(array $Columns) {
        return array_map([$this, 'GetMappedProperty'], $Columns);
    }
}

?>