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
    
    /**
     * @var Object\EntityMap
     */
    private $EntityMap;
    private $EntityType;
    /**
     * @var Relational\Table
     */
    private $PrimaryKeyTable;
    /**
     * @var IProperty[]
     */
    private $MappedProperties = array();
    private $MappedReviveColumns = array();
    private $MappedPersistColumns = array();
    /**
     * @var IPropertyMapping
     */
    private $PropertyMappings = array();
    /**
     * @var IDataPropertyColumnMapping[]
     */
    private $DataPropertyColumnMappings = array();
    /**
     * @var IEntityPropertyToOneRelationMapping[]
     */
    private $EntityPropertyToOneRelationMappings = array();
    /**
     * @var ICollectionPropertyToManyRelationMapping[]
     */
    private $CollectionPropertyToManyRelationMappings = array();
        
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
        if($PropertyMapping instanceof IDataPropertyColumnMapping) {
            $this->DataPropertyColumnMappings[$ProperyIdentifier] = $PropertyMapping;
            $this->MappedReviveColumns = array_merge($this->MappedReviveColumns, $PropertyMapping->GetReviveColumns());
            $this->MappedPersistColumns = array_merge($this->MappedPersistColumns, $PropertyMapping->GetPersistColumns());
        }
        else if($PropertyMapping instanceof IEntityPropertyToOneRelationMapping) {
            $this->EntityPropertyToOneRelationMappings[$ProperyIdentifier] = $PropertyMapping;
        }
        else if($PropertyMapping instanceof ICollectionPropertyToManyRelationMapping) {
            $this->CollectionPropertyToManyRelationMappings[$ProperyIdentifier] = $PropertyMapping;
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
    
    final public function GetDataPropertyColumnMappings() {
        return $this->DataPropertyColumnMappings;
    }
    
    final public function GetEntityPropertyToOneRelationMappings() {
        return $this->EntityPropertyToOneRelationMappings;
    }
    
    final public function GetCollectionPropertyToManyRelationMappings() {
        return $this->CollectionPropertyToManyRelationMappings;
    }
    
    public function ResultRow($ColumnData = array()) {
        return new Relational\ResultRow($this->MappedPersistColumns, $ColumnData);
    }

    final public function RelationalRequest(Object\IRequest $ObjectRequest) {
        $RelationalRequest = new Relational\Request(array(), $ObjectRequest->IsSingleEntity());
        $this->RelationalRequestConstraints($RelationalRequest);
        
        return $RelationalRequest;
    }
    protected function RelationalRequestConstraints(Relational\Request $RelationalRequest) { }
    
    private function VerifyPropertyColumnMapping(IProperty $Property) {
        $PropertyName = $Property;
        if(isset($this->PropertyColumnMappings[$PropertyName])) {
            return $this->PropertyColumnMappings[$PropertyName];
        }
        else {
            throw new \Storm\Core\Exceptions\UnmappedPropertyException();
        }
    }
    
    final public function Revive(DomainDatabaseMap $DomainDatabaseMap, Map $ResultRowRevivalDataMap) {
        foreach($this->DataPropertyColumnMappings as $PropertyColumnMapping) {
            $PropertyColumnMapping->Revive($ResultRowRevivalDataMap);
        }
        foreach($this->EntityPropertyToOneRelationMappings as $EntityPropertyToOneRelationMapping) {
            $EntityPropertyToOneRelationMapping->Revive($DomainDatabaseMap, $ResultRowRevivalDataMap);
        }
        foreach($this->CollectionPropertyToManyRelationMappings as $CollectionPropertyToManyRelationMapping) {
            $CollectionPropertyToManyRelationMapping->Revive($DomainDatabaseMap, $ResultRowRevivalDataMap);
        }
    }
    
    final public function Persist(DomainDatabaseMap $DomainDatabaseMap, Relational\Transaction $Transaction, array $PersistenceDataArray) {
        $PersistenceDataColumnDataMap = new Map();
        foreach($PersistenceDataArray as $PersistenceData) {
            $PersistenceDataColumnDataMap->Map($PersistenceData, new Relational\ResultRow($this->MappedPersistColumns));
        }
        foreach($this->DataPropertyColumnMappings as $PropertyColumnMapping) {
            $PropertyColumnMapping->Persist($PersistenceDataColumnDataMap);
        }
        
        foreach($this->EntityPropertyToOneRelationMappings as $EntityPropertyToOneRelationMapping) {
            $RelationshipChange = $DomainDatabaseMap->MapRelationshipChange($ObjectRelationshipChange);
            $EntityPropertyToOneRelationMapping->Persist($Transaction, $PersistenceDataColumnDataMap);
        }
        foreach($this->CollectionPropertyToManyRelationMappings as $CollectionPropertyToManyRelationMapping) {
            $CollectionPropertyToManyRelationMapping->Persist($Transaction, $PersistenceDataColumnDataMap);
        }
    }
    
    final public function Discard(Relational\Transaction $Transaction, Map $PersistenceDataColumnDataMap) {
        foreach($this->EntityPropertyToOneRelationMappings as $EntityPropertyToOneRelationMapping) {
            $EntityPropertyToOneRelationMapping->Persist($Transaction, $PersistenceDataColumnDataMap);
        }
        foreach($this->CollectionPropertyToManyRelationMappings as $CollectionPropertyToManyRelationMapping) {
            $CollectionPropertyToManyRelationMapping->Persist($Transaction, $PersistenceDataColumnDataMap);
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
            return $this->MappedReviveColumns;
        }
        $ColumnGroups = array_map([$this, 'GetMappedReviveColumns'], $Properties);
        $Columns = call_user_func_array('array_merge', $ColumnGroups);
        return $Columns;
    }
    
    final public function GetAllMappedPersistColumns(array $Properties = null) {
        if($Properties === null) {
            return $this->MappedPersistColumns;
        }
        $ColumnGroups = array_map([$this, 'GetMappedPersistColumns'], $Properties);
        $Columns = call_user_func_array('array_merge', $ColumnGroups);
        return $Columns;
    }

    final public function MapIdentityToPrimaryKey(Object\Identity $Identity) {
        $PrimaryKey = $this->PrimaryKeyTable->PrimaryKey();
        $Map = Map::From([$PrimaryKey], [$Identity]);
        foreach($this->DataPropertyColumnMappings as $Mapping) {
            $Property = $Mapping->GetProperty();
            if(isset($Identity[$Property])) {
                $Mapping->Persist($Map);
            }
        }
        
        return $PrimaryKey;
    }

    final public function MapPrimaryKeyToIdentity(Relational\PrimaryKey $PrimaryKey) {
        $Identity = $this->EntityMap->Identity();
        $Map = Map::From([$PrimaryKey], [$Identity]);
        foreach($this->DataPropertyColumnMappings as $Mapping) {
            $Columns = $Mapping->GetReviveColumns();
            foreach($Columns as $Column) {
                if(!isset($PrimaryKey[$Column])) {
                    break;
                }
            }
            $Mapping->Revive($Map);
        }
        
        return $Identity;
    }
}

?>