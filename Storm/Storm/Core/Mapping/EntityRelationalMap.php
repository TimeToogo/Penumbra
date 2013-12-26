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
    /**
     * @var IProperty
     */
    private $MappedProperties = array();
    /**
     * @var IPropertyMapping
     */
    private $PropertyMappings = array();
    /**
     * @var IPropertyColumnMapping
     */
    private $PropertyColumnMappings = array();
    /**
     * @var IPropertyRelationMapping
     */
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
        
        $Database = $DomainDatabaseMap->GetDatabase();
        $this->PrimaryKeyTable = $this->PrimaryKeyTable($Database);
        if(!($this->PrimaryKeyTable instanceof Relational\Table))
            throw new \UnexpectedValueException
                    ('Return value from ' . get_class($this) . '->PrimaryKeyTable() must be a valid Table');
        
        $Registrar = new Registrar(IPropertyMapping::IPropertyMappingType);
        $this->RegisterPropertyMappings($Registrar, $this->EntityMap, $Database);
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
        $ProperyIdentifier = $PropertyMapping->GetProperty()->GetIdentifier();
        if($PropertyMapping instanceof IPropertyColumnMapping) {
            $this->PropertyColumnMappings[$ProperyIdentifier] = $PropertyMapping;
        }
        else if($PropertyMapping instanceof IPropertyRelationMapping) {
            $this->PropertyRelationMappings[$ProperyIdentifier] = $PropertyMapping;
        }
        else {
            throw new \UnexpectedValueException('$PropertyMapping not instance of ^');//TODO: error messages
        }
        $this->MappedProperties[$ProperyIdentifier] = $PropertyMapping->GetProperty();
        $this->PropertyMappings[$ProperyIdentifier] = $PropertyMapping;
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
    
    private function VerifyPropertyColumnMapping(IProperty $Property) {
        $PropertyName = $Property;
        if(isset($this->PropertyColumnMappings[$PropertyName])) {
            return $this->PropertyColumnMappings[$PropertyName];
        }
        else {
            throw new \Storm\Core\Exceptions\UnmappedPropertyException();
        }
    }
    
    final public function Revive(RevivingContext $Context, Map $ResultRowStateMap) {
        foreach($this->PropertyColumnMappings as $PropertyColumnMapping) {
            $PropertyColumnMapping->Revive($ResultRowStateMap);
        }
        foreach($this->PropertyRelationMappings as $PropertyRelationMapping) {
            $PropertyRelationMapping->Revive($Context, $ResultRowStateMap);
        }
    }
    
    final public function Persist(PersistingContext $Context, TransactionalContext $TransactionalContext) {
        foreach($this->PropertyMappings as $PropertyMapping) {
            $PropertyMapping->Persist($Context, $TransactionalContext);
        }
    }
    
    final public function Discard(DiscardingContext $Context, TransactionalContext $TransactionalContext) {
        foreach($this->PropertyMappings as $PropertyMapping) {
            $PropertyMapping->Discard($Context, $TransactionalContext);
        }
    }
    
    final public function GetMappedReviveColumns(IProperty $Property) {
        $this->VerifyPropertyColumnMapping($Property)->GetReviveColumns();
    }
    
    final public function GetMappedPersistColumns(IProperty $Property) {
        $this->VerifyPropertyColumnMapping($Property)->GetPersistColumns();
    }
    
    final public function GetAllMappedReviveColumns(array $Properties = null) {
        if($Properties === null) {
            $Properties = $this->MappedProperties;
        }
        $ColumnGroups = array_map([$this, 'GetMappedReviveColumns'], $Properties);
        $Columns = call_user_func_array('array_merge', $ColumnGroups);
        return $Columns;
    }
    
    final public function GetAllMappedPersistColumns(array $Properties = null) {
        if($Properties === null) {
            $Properties = $this->MappedProperties;
        }
        $ColumnGroups = array_map([$this, 'GetMappedPersistColumns'], $Properties);
        $Columns = call_user_func_array('array_merge', $ColumnGroups);
        return $Columns;
    }

    final public function MapPropertyDataToColumnData(
            Object\PropertyData $PropertyData, 
            Relational\ColumnData $ColumnData) {
        foreach($this->PropertyColumnMappings as $Mapping) {
            $Property = $Mapping->GetProperty();
            if(isset($PropertyData[$Property])) {
                $Columns = $Mapping->GetPersistColumns();
                foreach($Column as $Column) {
                    $Value = $PropertyData[$Property];
                    $Column->Store($ColumnData, $Value);
                }
            }
        }
    }
    
    final public function MapColumnDataToPropertyData(
            Relational\ColumnData $ColumnData, 
            Object\PropertyData $PropertyData) {
        foreach($this->GetPropertyColumnMappings() as $Mapping) {
            $Columns = $Mapping->GetReviveColumns();
            $Property =  $Mapping->GetProperty();
            foreach ($Columns as $Column) {
                if(isset($ColumnData[$Column])) {
                   $PropertyData[$Property] = $Column->Retrieve($ColumnData);
                }
            }
        }
    }
}

?>